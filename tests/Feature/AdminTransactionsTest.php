<?php

use App\Models\ClickTransaction;
use App\Models\Section;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);
});

test('a non-admin cannot see the transactions page', function () {
    $student = User::factory()->create();

    $this->actingAs($student)->get(route('admin.transactions.index'))->assertForbidden();
});

test('an admin sees the transaction history with user, item and status', function () {
    $admin = User::factory()->create(['name' => 'Bosh admin']);
    $admin->assignRole('admin');

    $teacher = User::factory()->create();
    $student = User::factory()->create(['name' => 'Doniyor Aliyev']);
    $section = makePurchaseTopic($teacher, 30000)->section;

    ClickTransaction::create([
        'user_id' => $student->id, 'type' => 'purchase', 'purchasable_type' => Section::class,
        'purchasable_id' => $section->id, 'amount' => 30000, 'status' => 'paid', 'click_trans_id' => '111',
    ]);
    ClickTransaction::create([
        'user_id' => $student->id, 'type' => 'topup', 'amount' => 50000, 'status' => 'pending',
    ]);

    $response = $this->actingAs($admin)->get(route('admin.transactions.index'));

    $response->assertOk();
    $response->assertSee('Doniyor Aliyev');
    $response->assertSee('Algebra');
    $response->assertSee("Hisobni to'ldirish");
    $response->assertSee("To'landi");
    $response->assertSee('Kutilmoqda');
});

test('the transaction history can be filtered by status', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $student = User::factory()->create();

    ClickTransaction::create(['user_id' => $student->id, 'type' => 'topup', 'amount' => 10000, 'status' => 'paid']);
    ClickTransaction::create(['user_id' => $student->id, 'type' => 'topup', 'amount' => 20000, 'status' => 'cancelled']);

    $response = $this->actingAs($admin)->get(route('admin.transactions.index', ['status' => 'cancelled']));

    $response->assertOk();
    $response->assertSee('20 000', false);
    $response->assertDontSee('10 000', false);
});

test('the transaction history has no edit or delete controls', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $student = User::factory()->create();

    ClickTransaction::create(['user_id' => $student->id, 'type' => 'topup', 'amount' => 10000, 'status' => 'paid']);

    $response = $this->actingAs($admin)->get(route('admin.transactions.index'));

    $response->assertOk();
    $response->assertDontSee('o\'chirmoqchimisiz');
});
