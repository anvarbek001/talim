<?php

use Database\Seeders\RolePermissionSeeder;

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);
});

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new teacher users can register', function () {
    $response = $this->post('/register', [
        'name' => 'Test Teacher',
        'email' => 'teacher@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'role' => 'teacher',
    ]);

    $this->assertAuthenticated();
    expect(auth()->user()->hasRole('teacher'))->toBeTrue();
    $response->assertRedirect(route('dashboard', absolute: false));
});

test('new student users can register', function () {
    $response = $this->post('/register', [
        'name' => 'Test Student',
        'email' => 'student@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'role' => 'student',
    ]);

    $this->assertAuthenticated();
    expect(auth()->user()->hasRole('student'))->toBeTrue();
    $response->assertRedirect(route('student_dashboard', absolute: false));
});

test('registration requires a valid role', function () {
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'no-role@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertSessionHasErrors(['role']);
    $this->assertGuest();

    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'bad-role@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'role' => 'admin',
    ]);

    $response->assertSessionHasErrors(['role']);
    $this->assertGuest();
});
