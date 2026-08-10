<?php

namespace App\Services;

use App\Models\Book;
use App\Models\User;
use App\Repositories\Contracts\BookRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BookService
{
    public function __construct(
        protected BookRepositoryInterface $bookRepo,
        protected PurchaseService $purchaseServ,
    ) {}

    /**
     * @param  array{q?: string}  $filters
     */
    public function myBooks(int $userId, array $filters = []): LengthAwarePaginator
    {
        return $this->bookRepo->forUser($userId, $filters);
    }

    /**
     * @param  array{q?: string, teacher_id?: int}  $filters
     */
    public function all(array $filters = []): LengthAwarePaginator
    {
        return $this->bookRepo->all($filters);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function updateBook(Book $book, array $data): Book
    {
        return $this->bookRepo->update($book, [
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'price' => $data['price'] ?? 0,
        ]);
    }

    public function deleteBook(Book $book): bool
    {
        return DB::transaction(function () use ($book) {
            foreach ($book->files as $file) {
                Storage::disk('local')->delete($file->file_path);
            }

            return $this->bookRepo->delete($book);
        });
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

    /**
     * @param  array{q?: string}  $filters
     */
    public function catalog(int $userId, array $filters = []): LengthAwarePaginator
    {
        $q = $filters['q'] ?? null;
        $user = User::find($userId);

        return Book::with(['user', 'files', 'purchases' => fn ($query) => $query->where('user_id', $userId)])
            ->when($q, fn ($query, $search) => $query->where(
                fn ($sub) => $sub->where('title', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%"))
            ))
            ->latest()
            ->paginate(12)
            ->withQueryString()
            ->through(fn (Book $book) => [
                'id' => $book->id,
                'title' => $book->title,
                'description' => $book->description,
                'author' => $book->user->name,
                'files_count' => $book->files->count(),
                'price' => $book->price,
                // "Sotib olingan" — o'zi sotib olgan bo'lsa yoki muallifning
                // faol oylik obunasi bo'lsa ham (ikkalasi ham amal qilib turgan
                // bo'lishi kerak — muddati o'tgan xarid endi hisoblanmaydi).
                'purchased' => $user && $this->purchaseServ->hasAccess($user, $book),
                'created_at' => $book->created_at,
            ]);
    }

    /**
     * Whether $user may open the book's PDFs: either they own it, or they
     * have purchase access to it (or it's free).
     */
    public function canView(Book $book, User $user): bool
    {
        return $book->user_id === $user->id
            || $user->hasRole('admin')
            || $this->purchaseServ->hasAccess($user, $book);
    }
}
