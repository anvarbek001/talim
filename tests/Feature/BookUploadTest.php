<?php

use App\Models\Book;
use App\Models\BookFile;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('a teacher can upload a book with one pdf file', function () {
    Storage::fake('local');

    $teacher = User::factory()->create();
    $pdf = UploadedFile::fake()->create('kitob.pdf', 200, 'application/pdf');

    $response = $this->actingAs($teacher)->post(route('books.store'), [
        'title' => 'Algebra masalalar to\'plami',
        'description' => 'Tavsif',
        'price' => 0,
        'book_files' => [$pdf],
    ]);

    $response->assertRedirect(route('books.mine'));
    $response->assertSessionHasNoErrors();

    $book = Book::first();
    expect($book)->not->toBeNull();
    expect($book->title)->toBe('Algebra masalalar to\'plami');
    expect($book->isFree())->toBeTrue();

    $bookFile = BookFile::where('book_id', $book->id)->first();
    expect($bookFile)->not->toBeNull();
    Storage::disk('local')->assertExists($bookFile->file_path);
});

test('a teacher can upload a book with multiple pdf files', function () {
    Storage::fake('local');

    $teacher = User::factory()->create();

    $response = $this->actingAs($teacher)->post(route('books.store'), [
        'title' => 'Fizika qo\'llanma',
        'price' => 15000,
        'book_files' => [
            UploadedFile::fake()->create('1-qism.pdf', 100, 'application/pdf'),
            UploadedFile::fake()->create('2-qism.pdf', 100, 'application/pdf'),
        ],
    ]);

    $response->assertRedirect(route('books.mine'));

    $book = Book::first();
    expect($book->files)->toHaveCount(2);
    expect($book->price)->toBe(15000);
    expect($book->isFree())->toBeFalse();
});

test('book upload requires a title and at least one pdf file', function () {
    $teacher = User::factory()->create();

    $response = $this->actingAs($teacher)->post(route('books.store'), []);

    $response->assertSessionHasErrors(['title', 'book_files']);
});

test('book upload rejects non pdf files', function () {
    $teacher = User::factory()->create();

    $response = $this->actingAs($teacher)->post(route('books.store'), [
        'title' => 'Noto\'g\'ri format',
        'price' => 0,
        'book_files' => [UploadedFile::fake()->create('kitob.docx', 100, 'application/msword')],
    ]);

    $response->assertSessionHasErrors(['book_files.0']);
});

test('my books page only shows the authenticated teacher\'s own books', function () {
    $teacherA = User::factory()->create();
    $teacherB = User::factory()->create();

    $bookA = Book::create(['user_id' => $teacherA->id, 'title' => 'Kitob A', 'price' => 0]);
    $bookA->files()->create(['file_path' => 'books/a.pdf', 'original_name' => 'a.pdf']);

    $bookB = Book::create(['user_id' => $teacherB->id, 'title' => 'Kitob B', 'price' => 0]);
    $bookB->files()->create(['file_path' => 'books/b.pdf', 'original_name' => 'b.pdf']);

    $response = $this->actingAs($teacherA)->get(route('books.mine'));

    $response->assertOk();
    $response->assertSee('Kitob A');
    $response->assertDontSee('Kitob B');
});
