<?php

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);
});

test('a student visiting the teacher dashboard is redirected to their own dashboard', function () {
    $student = User::factory()->create();
    $student->assignRole('student');

    $response = $this->actingAs($student)->get(route('dashboard'));

    $response->assertRedirect(route('student_dashboard'));
});

test('a teacher visiting the student dashboard is redirected to their own dashboard', function () {
    $teacher = User::factory()->create();
    $teacher->assignRole('teacher');

    $response = $this->actingAs($teacher)->get(route('student_dashboard'));

    $response->assertRedirect(route('dashboard'));
});

test('a teacher can view their own dashboard', function () {
    $teacher = User::factory()->create();
    $teacher->assignRole('teacher');

    $response = $this->actingAs($teacher)->get(route('dashboard'));

    $response->assertOk();
});

test('a student can view their own dashboard', function () {
    $student = User::factory()->create();
    $student->assignRole('student');

    $response = $this->actingAs($student)->get(route('student_dashboard'));

    $response->assertOk();
});
