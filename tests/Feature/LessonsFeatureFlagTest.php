<?php

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);
    // The suite's phpunit.xml doesn't set FEATURE_LESSONS_ENABLED, so this
    // just documents the real production default explicitly.
    config(['features.lessons_enabled' => false]);
});

test('lesson routes 404 for teachers while the feature is disabled', function () {
    $teacher = User::factory()->create();

    $this->actingAs($teacher)->get(route('lesson'))->assertNotFound();
    $this->actingAs($teacher)->get(route('lessons.mine'))->assertNotFound();
});

test('lesson routes 404 for students while the feature is disabled', function () {
    $student = User::factory()->create();

    $this->actingAs($student)->get(route('student-lessons.index'))->assertNotFound();
});

test('the admin lessons route 404s while the feature is disabled', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $this->actingAs($admin)->get(route('admin.lessons.index'))->assertNotFound();
});

test('the teacher dashboard does not link to lessons while the feature is disabled', function () {
    $teacher = User::factory()->create();

    $response = $this->actingAs($teacher)->get(route('dashboard'));

    $response->assertOk();
    $response->assertDontSee(route('lessons.mine'), false);
    $response->assertDontSee(route('lesson'), false);
});

test('the student dashboard does not link to lessons while the feature is disabled', function () {
    $student = User::factory()->create();

    $response = $this->actingAs($student)->get(route('student_dashboard'));

    $response->assertOk();
    $response->assertDontSee(route('student-lessons.index'), false);
    $response->assertDontSee('Fanlar');
});

test('the admin panel does not link to lessons while the feature is disabled', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $response = $this->actingAs($admin)->get(route('admin.dashboard'));

    $response->assertOk();
    $response->assertDontSee(route('admin.lessons.index'), false);
});
