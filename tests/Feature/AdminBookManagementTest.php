<?php

use App\Models\Book;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);
});

test('an admin can view all teachers books', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $teacher = User::factory()->create();
    $teacher->assignRole('teacher');
    $book = Book::create(['user_id' => $teacher->id, 'title' => 'Fizika kitobi', 'price' => 5000]);
    $book->files()->create(['file_path' => 'books/x.pdf', 'original_name' => 'x.pdf']);

    $response = $this->actingAs($admin)->get(route('admin.books.index'));

    $response->assertOk();
    $response->assertSee('Fizika kitobi');
});

test('a non-admin is forbidden from the admin books list', function () {
    $teacher = User::factory()->create();
    $teacher->assignRole('teacher');

    $response = $this->actingAs($teacher)->get(route('admin.books.index'));

    $response->assertForbidden();
});

test('an admin can update any teachers book', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $teacher = User::factory()->create();
    $teacher->assignRole('teacher');
    $book = Book::create(['user_id' => $teacher->id, 'title' => 'Eski nom', 'price' => 1000]);

    $response = $this->actingAs($admin)->put(route('admin.books.update', $book), [
        'title' => 'Yangi nom',
        'description' => 'Yangilangan tavsif',
        'price' => 2000,
    ]);

    $response->assertRedirect(route('admin.books.index'));
    $book->refresh();
    expect($book->title)->toBe('Yangi nom');
    expect((int) $book->price)->toBe(2000);
});

test('an admin can delete any teachers book and its files are removed from disk', function () {
    Storage::fake('local');
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $teacher = User::factory()->create();
    $teacher->assignRole('teacher');
    $book = Book::create(['user_id' => $teacher->id, 'title' => 'Kitob', 'price' => 0]);
    Storage::disk('local')->put('books/1/file.pdf', 'content');
    $book->files()->create(['file_path' => 'books/1/file.pdf', 'original_name' => 'file.pdf']);

    $response = $this->actingAs($admin)->delete(route('admin.books.destroy', $book));

    $response->assertRedirect(route('admin.books.index'));
    expect(Book::find($book->id))->toBeNull();
    Storage::disk('local')->assertMissing('books/1/file.pdf');
});
