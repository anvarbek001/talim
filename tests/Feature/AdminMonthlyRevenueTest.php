<?php

use App\Models\Purchase;
use App\Models\Section;
use App\Models\User;
use App\Services\AdminStatisticsService;
use Database\Seeders\RolePermissionSeeder;

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);
});

test('monthly revenue splits gross revenue into the platform cut and teacher payouts per month', function () {
    $teacher = User::factory()->create();
    $student = User::factory()->create();
    $sectionThisMonth = makePurchaseTopic($teacher, 10000)->section;
    $sectionLastMonth = makePurchaseTopic($teacher, 20000)->section;

    // created_at/updated_at aren't mass-assignable on Purchase, so backdating
    // requires forceFill() after create() rather than passing them to create().
    Purchase::create([
        'user_id' => $student->id, 'purchasable_type' => Section::class, 'purchasable_id' => $sectionThisMonth->id,
        'price' => 10000,
    ])->forceFill(['created_at' => now(), 'updated_at' => now()])->save();

    Purchase::create([
        'user_id' => $student->id, 'purchasable_type' => Section::class, 'purchasable_id' => $sectionLastMonth->id,
        'price' => 20000,
    ])->forceFill(['created_at' => now()->subMonth(), 'updated_at' => now()->subMonth()])->save();

    $rows = app(AdminStatisticsService::class)->monthlyRevenue(3)->keyBy('month');

    $thisMonth = $rows->get(now()->format('Y-m'));
    expect($thisMonth['gross_revenue'])->toBe(10000.0);
    expect($thisMonth['platform_profit'])->toBe(2000.0);
    expect($thisMonth['teacher_payouts'])->toBe(8000.0);
    expect($thisMonth['purchases_count'])->toBe(1);

    $lastMonth = $rows->get(now()->subMonth()->format('Y-m'));
    expect($lastMonth['gross_revenue'])->toBe(20000.0);
    expect($lastMonth['platform_profit'])->toBe(4000.0);
    expect($lastMonth['teacher_payouts'])->toBe(16000.0);
});

test('months with no purchases show zero, not missing rows', function () {
    $rows = app(AdminStatisticsService::class)->monthlyRevenue(6);

    expect($rows)->toHaveCount(6);
    expect($rows->every(fn (array $row) => $row['gross_revenue'] === 0.0))->toBeTrue();
});

test('monthly revenue rows are ordered oldest to newest', function () {
    $rows = app(AdminStatisticsService::class)->monthlyRevenue(4)->pluck('month')->values()->all();

    $expected = collect(range(3, 0))->map(fn ($i) => now()->subMonths($i)->format('Y-m'))->all();

    expect($rows)->toBe($expected);
});

test('the admin dashboard renders the monthly statistics chart and table', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $response = $this->actingAs($admin)->get(route('admin.dashboard'));

    $response->assertOk();
    $response->assertSee('Oylik statistika');
    $response->assertSee('Platforma foydasi');
    $response->assertSee("O'qituvchilarga to'langan", false);
});
