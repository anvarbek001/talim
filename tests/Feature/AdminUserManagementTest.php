<?php

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);
});

test('an admin can view the users list', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $teacher = User::factory()->create(['name' => 'Aziz Karimov']);
    $teacher->assignRole('teacher');

    $response = $this->actingAs($admin)->get(route('admin.users.index'));

    $response->assertOk();
    $response->assertSee('Aziz Karimov');
});

test('a non-admin is forbidden from the users list', function () {
    $teacher = User::factory()->create();
    $teacher->assignRole('teacher');

    $response = $this->actingAs($teacher)->get(route('admin.users.index'));

    $response->assertForbidden();
});

test('an admin can create a new user with a role', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $response = $this->actingAs($admin)->post(route('admin.users.store'), [
        'name' => 'Yangi Ustoz',
        'email' => 'yangi.ustoz@talim.test',
        'password' => 'password123',
        'role' => 'teacher',
    ]);

    $response->assertRedirect(route('admin.users.index'));
    $user = User::where('email', 'yangi.ustoz@talim.test')->firstOrFail();
    expect($user->hasRole('teacher'))->toBeTrue();
});

test('an admin can update a user and change their role', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $student = User::factory()->create(['name' => 'Eski Ism']);
    $student->assignRole('student');

    $response = $this->actingAs($admin)->put(route('admin.users.update', $student), [
        'name' => 'Yangi Ism',
        'email' => $student->email,
        'role' => 'teacher',
    ]);

    $response->assertRedirect(route('admin.users.index'));
    $student->refresh();
    expect($student->name)->toBe('Yangi Ism');
    expect($student->hasRole('teacher'))->toBeTrue();
    expect($student->hasRole('student'))->toBeFalse();
});

test('an admin can delete another user', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $student = User::factory()->create();
    $student->assignRole('student');

    $response = $this->actingAs($admin)->delete(route('admin.users.destroy', $student));

    $response->assertRedirect(route('admin.users.index'));
    expect(User::find($student->id))->toBeNull();
});

test('an admin cannot delete their own account', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $response = $this->actingAs($admin)->delete(route('admin.users.destroy', $admin));

    $response->assertForbidden();
    expect(User::find($admin->id))->not->toBeNull();
});
