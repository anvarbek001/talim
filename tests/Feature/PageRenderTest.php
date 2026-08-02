<?php

use App\Models\User;

test('the student dashboard renders for an authenticated student', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('student_dashboard'));

    $response->assertOk();
    $response->assertSee('Testlarim');
});

test('the teacher tests page renders for an authenticated teacher', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('tests.index'));

    $response->assertOk();
    $response->assertSee('Testlarim');
});
