<?php

use App\Models\ClickTransaction;
use App\Models\Purchase;
use App\Models\Section;
use App\Models\User;

function clickSign(array $params, bool $isComplete): string
{
    $parts = [
        $params['click_trans_id'] ?? '',
        config('services.click.service_id'),
        config('services.click.secret_key'),
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

beforeEach(function () {
    config([
        'services.click.service_id' => 'test_service',
        'services.click.merchant_id' => 'test_merchant',
        'services.click.secret_key' => 'test_secret',
        'services.click.checkout_url' => 'https://my.click.uz/services/pay',
        'services.click.return_url' => 'http://localhost/student/payments',
    ]);
});

test('initiating a click payment creates a pending transaction and a checkout url', function () {
    $teacher = User::factory()->create();
    $student = User::factory()->create();
    $section = makePurchaseTopic($teacher, 75000)->section;

    $response = $this->actingAs($student)->post(route('click.pay', ['section', $section->id]));

    $transaction = ClickTransaction::first();
    expect($transaction)->not->toBeNull();
    expect($transaction->amount)->toBe(75000);
    expect($transaction->status)->toBe('pending');
    expect($transaction->user_id)->toBe($student->id);

    $response->assertRedirect();
    expect($response->headers->get('Location'))->toContain('merchant_id=test_merchant');
    // transaction_param embeds the student's name (Click shows it back to the
    // payer as "ФИО отправителя"), followed by our own id for lookup.
    expect($response->headers->get('Location'))->toContain('%23'.$transaction->id);
});

test('the transaction id survives being embedded in a name-prefixed transaction_param', function () {
    $teacher = User::factory()->create();
    $student = User::factory()->create(['name' => "O'ktam Yusupov"]);
    $section = makePurchaseTopic($teacher, 30000)->section;

    $transaction = ClickTransaction::create([
        'user_id' => $student->id, 'type' => 'purchase', 'purchasable_type' => Section::class,
        'purchasable_id' => $section->id, 'amount' => 30000, 'status' => 'pending',
    ]);

    $payload = [
        'click_trans_id' => '321',
        'merchant_trans_id' => "O'ktam Yusupov #{$transaction->id}",
        'amount' => 30000, 'action' => 0, 'sign_time' => now()->toDateTimeString(),
    ];
    $payload['sign_string'] = clickSign($payload, false);

    $response = $this->post(route('click.prepare'), $payload);

    $response->assertJsonPath('error', 0);
    $response->assertJsonPath('merchant_prepare_id', $transaction->id);
    expect($transaction->fresh()->status)->toBe('prepared');
});

test('a student can initiate a balance top-up', function () {
    $student = User::factory()->create();

    $response = $this->actingAs($student)->post(route('click.topup'), ['amount' => 50000]);

    $transaction = ClickTransaction::first();
    expect($transaction)->not->toBeNull();
    expect($transaction->type)->toBe('topup');
    expect($transaction->purchasable_type)->toBeNull();
    expect($transaction->amount)->toBe(50000);

    $response->assertRedirect();
    expect($response->headers->get('Location'))->toContain('%23'.$transaction->id);
});

test('a top-up below the minimum amount is rejected', function () {
    $student = User::factory()->create();

    $response = $this->actingAs($student)->post(route('click.topup'), ['amount' => 100]);

    $response->assertSessionHasErrors('amount');
    expect(ClickTransaction::count())->toBe(0);
});

test('completing a top-up transaction credits the student balance', function () {
    $student = User::factory()->create();
    $student->forceFill(['balance' => 2000])->save();

    $transaction = ClickTransaction::create([
        'user_id' => $student->id, 'type' => 'topup', 'amount' => 50000, 'status' => 'prepared',
        'click_trans_id' => '555',
    ]);

    $payload = [
        'click_trans_id' => '555', 'merchant_trans_id' => (string) $transaction->id,
        'merchant_prepare_id' => (string) $transaction->id,
        'amount' => 50000, 'action' => 1, 'sign_time' => now()->toDateTimeString(),
        'error' => 0, 'click_paydoc_id' => 'DOC2',
    ];
    $payload['sign_string'] = clickSign($payload, true);

    $response = $this->post(route('click.complete'), $payload);

    $response->assertJsonPath('error', 0);
    expect($transaction->fresh()->status)->toBe('paid');
    expect($student->fresh()->balance)->toBe(52000);
});

test('a free item cannot be paid for via click', function () {
    $teacher = User::factory()->create();
    $student = User::factory()->create();
    $section = makePurchaseTopic($teacher, 0)->section;

    $this->actingAs($student)->post(route('click.pay', ['section', $section->id]))->assertStatus(422);
});

test('click prepare webhook rejects a bad signature', function () {
    $teacher = User::factory()->create();
    $student = User::factory()->create();
    $section = makePurchaseTopic($teacher, 40000)->section;

    $transaction = ClickTransaction::create([
        'user_id' => $student->id, 'purchasable_type' => Section::class,
        'purchasable_id' => $section->id, 'amount' => 40000, 'status' => 'pending',
    ]);

    $response = $this->post(route('click.prepare'), [
        'click_trans_id' => '123', 'merchant_trans_id' => $transaction->id,
        'amount' => 40000, 'action' => 0, 'sign_time' => now()->toDateTimeString(),
        'sign_string' => 'not-the-real-signature',
    ]);

    $response->assertOk();
    $response->assertJsonPath('error', -1);
    expect($transaction->fresh()->status)->toBe('pending');
});

test('click prepare webhook succeeds with a valid signature and matching amount', function () {
    $teacher = User::factory()->create();
    $student = User::factory()->create();
    $section = makePurchaseTopic($teacher, 40000)->section;

    $transaction = ClickTransaction::create([
        'user_id' => $student->id, 'purchasable_type' => Section::class,
        'purchasable_id' => $section->id, 'amount' => 40000, 'status' => 'pending',
    ]);

    $payload = [
        'click_trans_id' => '999', 'merchant_trans_id' => (string) $transaction->id,
        'amount' => 40000, 'action' => 0, 'sign_time' => now()->toDateTimeString(),
    ];
    $payload['sign_string'] = clickSign($payload, false);

    $response = $this->post(route('click.prepare'), $payload);

    $response->assertOk();
    $response->assertJsonPath('error', 0);
    $response->assertJsonPath('merchant_prepare_id', $transaction->id);
    expect($transaction->fresh()->status)->toBe('prepared');
});

test('click prepare webhook rejects a mismatched amount', function () {
    $teacher = User::factory()->create();
    $student = User::factory()->create();
    $section = makePurchaseTopic($teacher, 40000)->section;

    $transaction = ClickTransaction::create([
        'user_id' => $student->id, 'purchasable_type' => Section::class,
        'purchasable_id' => $section->id, 'amount' => 40000, 'status' => 'pending',
    ]);

    $payload = [
        'click_trans_id' => '999', 'merchant_trans_id' => (string) $transaction->id,
        'amount' => 1, 'action' => 0, 'sign_time' => now()->toDateTimeString(),
    ];
    $payload['sign_string'] = clickSign($payload, false);

    $response = $this->post(route('click.prepare'), $payload);

    $response->assertJsonPath('error', -2);
});

test('click complete webhook creates the purchase and marks the transaction paid', function () {
    $teacher = User::factory()->create();
    $student = User::factory()->create();
    $section = makePurchaseTopic($teacher, 40000)->section;

    $transaction = ClickTransaction::create([
        'user_id' => $student->id, 'purchasable_type' => Section::class,
        'purchasable_id' => $section->id, 'amount' => 40000, 'status' => 'prepared',
        'click_trans_id' => '999',
    ]);

    $payload = [
        'click_trans_id' => '999', 'merchant_trans_id' => (string) $transaction->id,
        'merchant_prepare_id' => (string) $transaction->id,
        'amount' => 40000, 'action' => 1, 'sign_time' => now()->toDateTimeString(),
        'error' => 0, 'click_paydoc_id' => 'DOC1',
    ];
    $payload['sign_string'] = clickSign($payload, true);

    $response = $this->post(route('click.complete'), $payload);

    $response->assertJsonPath('error', 0);
    expect($transaction->fresh()->status)->toBe('paid');
    expect(Purchase::where('user_id', $student->id)
        ->where('purchasable_type', Section::class)
        ->where('purchasable_id', $section->id)
        ->exists())->toBeTrue();
});

test('click complete webhook cancellation does not create a purchase', function () {
    $teacher = User::factory()->create();
    $student = User::factory()->create();
    $section = makePurchaseTopic($teacher, 40000)->section;

    $transaction = ClickTransaction::create([
        'user_id' => $student->id, 'purchasable_type' => Section::class,
        'purchasable_id' => $section->id, 'amount' => 40000, 'status' => 'prepared',
        'click_trans_id' => '999',
    ]);

    $payload = [
        'click_trans_id' => '999', 'merchant_trans_id' => (string) $transaction->id,
        'merchant_prepare_id' => (string) $transaction->id,
        'amount' => 40000, 'action' => 1, 'sign_time' => now()->toDateTimeString(),
        'error' => -9, 'error_note' => 'Cancelled by user',
    ];
    $payload['sign_string'] = clickSign($payload, true);

    $response = $this->post(route('click.complete'), $payload);

    $response->assertJsonPath('error', 0);
    expect($transaction->fresh()->status)->toBe('cancelled');
    expect(Purchase::count())->toBe(0);
});
