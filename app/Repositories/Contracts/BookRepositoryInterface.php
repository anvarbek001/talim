<?php

namespace App\Repositories\Contracts;

use App\Models\Book;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface BookRepositoryInterface
{
    /**
     * @param  array{q?: string, teacher_id?: int, per_page?: int}  $filters
     */
    public function all(array $filters = []): LengthAwarePaginator;

    public function create(array $data);

    public function find(int $id);

    /**
     * @param  array{q?: string, per_page?: int}  $filters
     */
    public function forUser(int $userId, array $filters = []): LengthAwarePaginator;

    public function update(Book $book, array $data): Book;

    public function delete(Book $book): bool;
}
