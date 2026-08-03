<?php

namespace App\Services;

use App\Models\Book;
use App\Models\User;
use App\Repositories\Contracts\BookRepositoryInterface;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class BookService
{
    public function __construct(
        protected BookRepositoryInterface $bookRepo,
        protected PurchaseService $purchaseServ,
    ) {}

    public function myBooks(int $userId): EloquentCollection
    {
        return $this->bookRepo->forUser($userId);
    }

    public function createBook(array $data, int $userId): Book
    {
        return DB::transaction(function () use ($data, $userId) {
            $book = $this->bookRepo->create([
                'user_id' => $userId,
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'price' => $data['price'] ?? 0,
            ]);

            foreach ($data['book_files'] as $file) {
                $path = $file->store("books/{$book->id}", 'local');
                $book->files()->create([
                    'file_path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                ]);
            }

            return $book;
        });
    }

    public function catalog(int $userId): Collection
    {
        return Book::with(['user', 'files', 'purchases' => fn ($q) => $q->where('user_id', $userId)])
            ->latest()
            ->get()
            ->map(fn (Book $book) => [
                'id' => $book->id,
                'title' => $book->title,
                'description' => $book->description,
                'author' => $book->user->name,
                'files_count' => $book->files->count(),
                'price' => $book->price,
                'purchased' => $book->purchases->isNotEmpty(),
                'created_at' => $book->created_at,
            ]);
    }

    /**
     * Whether $user may open the book's PDFs: either they own it, or they
     * have purchase access to it (or it's free).
     */
    public function canView(Book $book, User $user): bool
    {
        return $book->user_id === $user->id || $this->purchaseServ->hasAccess($user, $book);
    }
}
