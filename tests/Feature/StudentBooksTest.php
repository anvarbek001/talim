<?php

use App\Models\Book;
use App\Models\Purchase;
use App\Models\User;
use App\Services\PurchaseService;
use Illuminate\Support\Facades\Storage;

function makeBook(User $teacher, int $price = 0): Book
{
    $book = Book::create(['user_id' => $teacher->id, 'title' => 'Test kitobi', 'price' => $price]);
    $book->files()->create(['file_path' => "books/{$book->id}/kitob.pdf", 'original_name' => 'kitob.pdf']);

    return $book;
}

test('student catalog lists books from multiple teachers', function () {
    $teacherA = User::factory()->create();
    $teacherB = User::factory()->create();
    $student = User::factory()->create();

    $bookA = Book::create(['user_id' => $teacherA->id, 'title' => 'Kitob A', 'price' => 0]);
    $bookA->files()->create(['file_path' => "books/{$bookA->id}/a.pdf", 'original_name' => 'a.pdf']);

    $bookB = Book::create(['user_id' => $teacherB->id, 'title' => 'Kitob B', 'price' => 0]);
    $bookB->files()->create(['file_path' => "books/{$bookB->id}/b.pdf", 'original_name' => 'b.pdf']);

    $response = $this->actingAs($student)->get(route('student-books.index'));

    $response->assertOk();
    $response->assertSee('Kitob A');
    $response->assertSee('Kitob B');
});

test('a student is blocked from an unpurchased paid book and sees the locked view', function () {
    $teacher = User::factory()->create();
    $student = User::factory()->create();
    $book = makeBook($teacher, 10000);

    $response = $this->actingAs($student)->get(route('books.view', $book));

    $response->assertOk();
    $response->assertSee('Sotib olish');
});

test('a student can open a free book without purchasing', function () {
    $teacher = User::factory()->create();
    $student = User::factory()->create();
    $book = makeBook($teacher, 0);

    $response = $this->actingAs($student)->get(route('books.view', $book));

    $response->assertOk();
    $response->assertDontSee('Sotib olish');
});

test('a student can open a book after purchasing it', function () {
    $teacher = User::factory()->create();
    $student = User::factory()->create();
    $book = makeBook($teacher, 10000);

    $this->actingAs($student)->post(route('student-purchases.store', ['book', $book->id]));

    $response = $this->actingAs($student)->get(route('books.view', $book));

    $response->assertOk();
    $response->assertDontSee('Sotib olish');
});

test('the owning teacher can view their own priced book without purchasing', function () {
    $teacher = User::factory()->create();
    $book = makeBook($teacher, 10000);

    $response = $this->actingAs($teacher)->get(route('books.view', $book));

    $response->assertOk();
    $response->assertDontSee('Sotib olish');
});

test('the stream route returns 403 for a student without access', function () {
    $teacher = User::factory()->create();
    $student = User::factory()->create();
    $book = makeBook($teacher, 10000);

    $response = $this->actingAs($student)->get(route('books.stream', [$book, $book->files->first()]));

    $response->assertForbidden();
});

test('the stream route returns 404 when the book file does not belong to the book', function () {
    $teacher = User::factory()->create();
    $bookA = makeBook($teacher, 0);
    $bookB = makeBook($teacher, 0);

    $response = $this->actingAs($teacher)->get(route('books.stream', [$bookA, $bookB->files->first()]));

    $response->assertNotFound();
});

test('the stream response uses inline disposition for a free book', function () {
    $teacher = User::factory()->create();
    $student = User::factory()->create();
    $book = makeBook($teacher, 0);

    Storage::disk('local')->put($book->files->first()->file_path, '%PDF-1.4 fake');

    $response = $this->actingAs($student)->get(route('books.stream', [$book, $book->files->first()]));

    $response->assertOk();
    $response->assertHeader('Content-Type', 'application/pdf');
    expect($response->headers->get('Content-Disposition'))->toContain('inline');
    expect($response->headers->get('Content-Disposition'))->not->toContain('attachment');
});

test('purchasing the same book twice does not create a duplicate purchase', function () {
    $teacher = User::factory()->create();
    $student = User::factory()->create();
    $book = makeBook($teacher, 10000);

    $this->actingAs($student)->post(route('student-purchases.store', ['book', $book->id]));
    $this->actingAs($student)->post(route('student-purchases.store', ['book', $book->id]));

    expect(Purchase::where('user_id', $student->id)->where('purchasable_id', $book->id)->where('purchasable_type', Book::class)->count())->toBe(1);
});

test('a student cannot purchase a free book', function () {
    $teacher = User::factory()->create();
    $student = User::factory()->create();
    $book = makeBook($teacher, 0);

    app(PurchaseService::class)->purchase($student, $book);
})->throws(Exception::class, 'Bu material bepul — xarid qilish shart emas');
