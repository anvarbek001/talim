<?php

use App\Models\Book;
use App\Models\Purchase;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);
});

test('an admin can view all purchases', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $teacher = User::factory()->create();
    $teacher->assignRole('teacher');
    $buyer = User::factory()->create();
    $book = Book::create(['user_id' => $teacher->id, 'title' => 'Kimyo kitobi', 'price' => 5000]);
    $book->purchases()->create(['user_id' => $buyer->id, 'price' => 5000]);

    $response = $this->actingAs($admin)->get(route('admin.purchases.index'));

    $response->assertOk();
    $response->assertSee('Kimyo kitobi');
});

test('a non-admin is forbidden from the admin purchases list', function () {
    $teacher = User::factory()->create();
    $teacher->assignRole('teacher');

    $response = $this->actingAs($teacher)->get(route('admin.purchases.index'));

    $response->assertForbidden();
});

test('an admin can delete a purchase record', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $teacher = User::factory()->create();
    $teacher->assignRole('teacher');
    $buyer = User::factory()->create();
    $book = Book::create(['user_id' => $teacher->id, 'title' => 'Kitob', 'price' => 5000]);
    $purchase = $book->purchases()->create(['user_id' => $buyer->id, 'price' => 5000]);

    $response = $this->actingAs($admin)->delete(route('admin.purchases.destroy', $purchase));

    $response->assertRedirect(route('admin.purchases.index'));
    expect(Purchase::find($purchase->id))->toBeNull();
});
