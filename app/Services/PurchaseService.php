<?php

namespace App\Services;

use App\Contracts\Purchasable;
use App\Contracts\Subscribable;
use App\Contracts\TeacherOwned;
use App\Models\Purchase;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;

class PurchaseService
{
    /**
     * Buys $purchasable for $user using their platform balance — deducts the
     * price and creates (or renews) the Purchase row atomically. Throws if
     * the item is free (nothing to buy) or the balance can't cover the price.
     *
     * Subscribable items (Section, Book, a teacher's own subscription) grant
     * 1 oy (one month) of access — an existing but expired purchase gets
     * re-charged at the *current* price and its expiry pushed a month out.
     * A still-active purchase is returned as-is (idempotent, no double charge).
     */
    public function purchase(User $user, Purchasable $purchasable): Purchase
    {
        $existing = Purchase::where('user_id', $user->id)
            ->where('purchasable_type', $purchasable::class)
            ->where('purchasable_id', $purchasable->id)
            ->first();

        if ($existing && $existing->isActive()) {
            return $existing;
        }

        if ($purchasable->isFree()) {
            throw new Exception('Bu material bepul — xarid qilish shart emas', 422);
        }

        return DB::transaction(function () use ($user, $purchasable, $existing) {
            // Balansni tekshirish va yechish paytida poyga holatiga (race
            // condition) yo'l qo'ymaslik uchun qatorni qulflab olamiz.
            $lockedUser = User::whereKey($user->id)->lockForUpdate()->first();

            if ($lockedUser->balance < $purchasable->price) {
                throw new Exception("Balansingizda mablag' yetarli emas. Avval hisobingizni to'ldiring.", 422);
            }

            $lockedUser->decrement('balance', $purchasable->price);

            $attributes = [
                'price' => $purchasable->price,
                'expires_at' => $purchasable instanceof Subscribable ? now()->addMonth() : null,
            ];

            if ($existing) {
                $existing->update($attributes);

                return $existing->fresh();
            }

            return Purchase::create([
                'user_id' => $user->id,
                'purchasable_type' => $purchasable::class,
                'purchasable_id' => $purchasable->id,
                ...$attributes,
            ]);
        });
    }

    /**
     * True when $user can open $purchasable right now — either they bought
     * it directly (still-active purchase), it's free, or — for content that
     * belongs to a teacher (Section, Book) — they hold an active monthly
     * subscription to that teacher instead.
     */
    public function hasAccess(User $user, Purchasable $purchasable): bool
    {
        if ($purchasable->isFree() || $purchasable->isPurchasedBy($user)) {
            return true;
        }

        if ($purchasable instanceof TeacherOwned) {
            return $this->hasActiveTeacherSubscription($user, $purchasable->teacherId());
        }

        return false;
    }

    public function hasActiveTeacherSubscription(User $student, int $teacherId): bool
    {
        return Purchase::where('user_id', $student->id)
            ->where('purchasable_type', User::class)
            ->where('purchasable_id', $teacherId)
            ->where(fn ($query) => $query->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->exists();
    }
}
