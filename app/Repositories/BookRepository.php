<?php

namespace App\Repositories;

use App\Models\Book;
use App\Repositories\Contracts\BookRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Override;

class BookRepository implements BookRepositoryInterface
{
    public function __construct(
        protected Book $model
    ) {}

    /**
     * @param  array{q?: string, teacher_id?: int, per_page?: int}  $filters
     */
    #[Override]
    public function all(array $filters = []): LengthAwarePaginator
    {
        return $this->model
            ->with(['user', 'files'])
            ->when($filters['q'] ?? null, fn ($query, $search) => $query->where('title', 'like', "%{$search}%"))
            ->when($filters['teacher_id'] ?? null, fn ($query, $teacherId) => $query->where('user_id', $teacherId))
            ->latest()
            ->paginate($filters['per_page'] ?? 20)
            ->withQueryString();
    }

    #[Override]
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    #[Override]
    public function find(int $id)
    {
        return $this->model->with(['user', 'files'])->find($id);
    }

    /**
     * @param  array{q?: string, per_page?: int}  $filters
     */
    #[Override]
    public function forUser(int $userId, array $filters = []): LengthAwarePaginator
    {
        return $this
            ->model
            ->where('user_id', $userId)
            ->with('files')
            ->when($filters['q'] ?? null, fn ($query, $search) => $query->where('title', 'like', "%{$search}%"))
            ->latest()
            ->paginate($filters['per_page'] ?? 12)
            ->withQueryString();
    }

    #[Override]
    public function update(Book $book, array $data): Book
    {
        $book->update($data);

        return $book;
    }

    #[Override]
    public function delete(Book $book): bool
    {
        return $book->delete();
    }
}
