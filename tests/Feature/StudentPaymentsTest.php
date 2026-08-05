<?php

use App\Models\Purchase;
use App\Models\Section;
use App\Models\User;

test('guests cannot see the payments page', function () {
    $this->get(route('student-payments.index'))->assertRedirect(route('login'));
});

test('a student only sees their own purchase history with the correct total', function () {
    $teacher = User::factory()->create();
    $studentA = User::factory()->create();
    $studentB = User::factory()->create();

    $section = makePurchaseTopic($teacher, 50000)->section;

    Purchase::create([
        'user_id' => $studentA->id, 'purchasable_type' => Section::class,
        'purchasable_id' => $section->id, 'price' => 50000,
    ]);
    Purchase::create([
        'user_id' => $studentB->id, 'purchasable_type' => Section::class,
        'purchasable_id' => $section->id, 'price' => 50000,
    ]);

    $response = $this->actingAs($studentA)->get(route('student-payments.index'));

    $response->assertOk();
    $response->assertSee('Algebra');
    $response->assertSee('50 000', false);
    $response->assertSee("Bo'lim");
});

test('a student with no purchases sees an empty state', function () {
    $student = User::factory()->create();

    $response = $this->actingAs($student)->get(route('student-payments.index'));

    $response->assertOk();
    $response->assertSee('Hali hech narsa sotib olinmagan');
});
