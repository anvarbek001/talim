<?php

namespace App\Services;

use App\Models\Book;
use App\Models\ClickTransaction;
use App\Models\DtmTest;
use App\Models\LanguageExamTest;
use App\Models\Purchase;
use App\Models\Section;
use App\Models\SertifikatTest;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Click.uz Merchant Shop-API integration — https://docs.click.uz/en/click-request/
 *
 * Flow: initiate() creates a pending ClickTransaction and returns the
 * checkout URL the student is redirected to. Click then calls our
 * prepare()/complete() webhooks (signed with CLICK_SECRET_KEY) to confirm
 * the payment; complete() is what actually creates the Purchase row.
 *
 * Won't do anything useful until CLICK_MERCHANT_ID / CLICK_SERVICE_ID /
 * CLICK_SECRET_KEY are filled in .env (see config/services.php).
 */
class ClickPaymentService
{
    /** @var array<string, class-string> */
    protected const TYPE_MAP = [
        'section' => Section::class,
        'dtm' => DtmTest::class,
        'sertifikat' => SertifikatTest::class,
        'language_exam' => LanguageExamTest::class,
        'book' => Book::class,
    ];

    public const MIN_TOPUP_AMOUNT = 5000;

    /**
     * Creates a pending transaction and returns the Click checkout URL to
     * redirect the student to — buys a specific item directly.
     */
    public function initiate(User $user, string $type, int $id): string
    {
        $class = self::TYPE_MAP[$type] ?? null;

        abort_if($class === null, 404);

        $purchasable = $class::findOrFail($id);

        abort_if($purchasable->isFree(), 422, "Bu material bepul — to'lov qilish shart emas");
        abort_if($purchasable->isPurchasedBy($user), 422, 'Bu material allaqachon sotib olingan');

        $transaction = ClickTransaction::create([
            'user_id' => $user->id,
            'type' => 'purchase',
            'purchasable_type' => $class,
            'purchasable_id' => $purchasable->id,
            'amount' => (int) $purchasable->price,
            'status' => 'pending',
        ]);

        return $this->checkoutUrl($transaction);
    }

    /**
     * Creates a pending "top up" transaction (no specific item attached) and
     * returns the Click checkout URL — Click's complete() webhook credits
     * the user's balance once payment is confirmed.
     */
    public function initiateTopUp(User $user, int $amount): string
    {
        abort_if($amount < self::MIN_TOPUP_AMOUNT, 422, 'Eng kamida '.number_format(self::MIN_TOPUP_AMOUNT, 0, '.', ' ')." so'm to'ldirish mumkin");

        $transaction = ClickTransaction::create([
            'user_id' => $user->id,
            'type' => 'topup',
            'purchasable_type' => null,
            'purchasable_id' => null,
            'amount' => $amount,
            'status' => 'pending',
        ]);

        return $this->checkoutUrl($transaction);
    }

    public function checkoutUrl(ClickTransaction $transaction): string
    {
        $query = http_build_query([
            'service_id' => config('services.click.service_id'),
            'merchant_id' => config('services.click.merchant_id'),
            'amount' => $transaction->amount,
            'transaction_param' => $this->formatTransactionParam($transaction),
            'return_url' => config('services.click.return_url'),
        ]);

        return config('services.click.checkout_url').'?'.$query;
    }

    /**
     * Click shows this raw value back to the payer on its checkout page
     * under "ФИО отправителя" ("sender's full name") — a plain numeric ID
     * there looks broken, so the student's name is embedded alongside our
     * own transaction ID (still parsed back out by extractTransactionId()).
     */
    protected function formatTransactionParam(ClickTransaction $transaction): string
    {
        $name = trim((string) ($transaction->user?->name)) ?: 'Talaba';

        return "{$name} #{$transaction->id}";
    }

    /**
     * Recovers our internal ClickTransaction id from Click's echoed-back
     * merchant_trans_id, which formatTransactionParam() turned into
     * "Ism Familya #<id>" instead of a bare id.
     */
    protected function extractTransactionId(array $params): ?int
    {
        $raw = (string) ($params['merchant_trans_id'] ?? '');

        if (preg_match('/#(\d+)\s*$/', $raw, $matches)) {
            return (int) $matches[1];
        }

        return is_numeric($raw) ? (int) $raw : null;
    }

    /**
     * Click's "Prepare" step (action=0) — confirms the transaction exists
     * and the amount matches before the user is actually charged.
     *
     * @param  array<string, mixed>  $params
     * @return array<string, mixed>
     */
    public function prepare(array $params): array
    {
        $response = [
            'click_trans_id' => $params['click_trans_id'] ?? null,
            'merchant_trans_id' => $params['merchant_trans_id'] ?? null,
        ];

        if (! $this->hasValidSignature($params, false)) {
            return $response + $this->error(-1, 'SIGN CHECK FAILED');
        }

        $transaction = ClickTransaction::find($this->extractTransactionId($params));

        if (! $transaction) {
            return $response + $this->error(-5, 'Transaction does not exist');
        }

        if ((int) ($params['amount'] ?? 0) !== (int) $transaction->amount) {
            return $response + $this->error(-2, 'Incorrect parameter amount');
        }

        if ($transaction->isPaid()) {
            return $response + $this->error(-4, 'Already paid');
        }

        $transaction->update([
            'click_trans_id' => $params['click_trans_id'] ?? null,
            'status' => 'prepared',
        ]);

        return $response + [
            'merchant_prepare_id' => $transaction->id,
            'error' => 0,
            'error_note' => 'Success',
        ];
    }

    /**
     * Click's "Complete" step (action=1) — the payment has actually been
     * taken (or cancelled, if error < 0), so this is where the Purchase
     * row gets created.
     *
     * @param  array<string, mixed>  $params
     * @return array<string, mixed>
     */
    public function complete(array $params): array
    {
        $response = [
            'click_trans_id' => $params['click_trans_id'] ?? null,
            'merchant_trans_id' => $params['merchant_trans_id'] ?? null,
        ];

        if (! $this->hasValidSignature($params, true)) {
            return $response + $this->error(-1, 'SIGN CHECK FAILED');
        }

        $transaction = ClickTransaction::find($this->extractTransactionId($params));

        if (! $transaction) {
            return $response + $this->error(-5, 'Transaction does not exist');
        }

        if ((int) ($params['amount'] ?? 0) !== (int) $transaction->amount) {
            return $response + $this->error(-2, 'Incorrect parameter amount');
        }

        if ($transaction->isPaid()) {
            return $response + ['merchant_confirm_id' => $transaction->id, 'error' => 0, 'error_note' => 'Already confirmed'];
        }

        if ((int) ($params['error'] ?? 0) < 0) {
            $transaction->update([
                'status' => 'cancelled',
                'error_code' => $params['error'],
                'error_note' => $params['error_note'] ?? null,
            ]);

            return $response + ['error' => 0, 'error_note' => 'Success'];
        }

        if ($transaction->type === 'topup') {
            DB::transaction(function () use ($transaction, $params) {
                $transaction->update([
                    'status' => 'paid',
                    'click_paydoc_id' => $params['click_paydoc_id'] ?? null,
                ]);

                User::whereKey($transaction->user_id)->increment('balance', $transaction->amount);
            });

            return $response + [
                'merchant_confirm_id' => $transaction->id,
                'error' => 0,
                'error_note' => 'Success',
            ];
        }

        DB::transaction(function () use ($transaction, $params) {
            $transaction->update([
                'status' => 'paid',
                'click_paydoc_id' => $params['click_paydoc_id'] ?? null,
            ]);

            $alreadyPurchased = Purchase::where('user_id', $transaction->user_id)
                ->where('purchasable_type', $transaction->purchasable_type)
                ->where('purchasable_id', $transaction->purchasable_id)
                ->exists();

            if (! $alreadyPurchased) {
                Purchase::create([
                    'user_id' => $transaction->user_id,
                    'purchasable_type' => $transaction->purchasable_type,
                    'purchasable_id' => $transaction->purchasable_id,
                    'price' => $transaction->amount,
                ]);
            }
        });

        return $response + [
            'merchant_confirm_id' => $transaction->id,
            'error' => 0,
            'error_note' => 'Success',
        ];
    }

    /**
     * @param  array<string, mixed>  $params
     */
    protected function hasValidSignature(array $params, bool $isComplete): bool
    {
        $secretKey = config('services.click.secret_key');

        if (! $secretKey || ! isset($params['sign_string'])) {
            return false;
        }

        return hash_equals($this->signString($params, $isComplete), (string) $params['sign_string']);
    }

    /**
     * @param  array<string, mixed>  $params
     */
    protected function signString(array $params, bool $isComplete): string
    {
        $serviceId = config('services.click.service_id');
        $secretKey = config('services.click.secret_key');

        $parts = [
            $params['click_trans_id'] ?? '',
            $serviceId,
            $secretKey,
            $params['merchant_trans_id'] ?? '',
        ];

        if ($isComplete) {
            $parts[] = $params['merchant_prepare_id'] ?? '';
        }

        $parts[] = $params['amount'] ?? '';
        $parts[] = $params['action'] ?? '';
        $parts[] = $params['sign_time'] ?? '';

        return md5(implode('', $parts));
    }

    /**
     * @return array{error: int, error_note: string}
     */
    protected function error(int $code, string $note): array
    {
        return ['error' => $code, 'error_note' => $note];
    }
}
