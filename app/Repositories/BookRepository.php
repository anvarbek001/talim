<?php

namespace App\Repositories;

use App\Models\Book;
use App\Repositories\Contracts\BookRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Override;

class BookRepository implements BookRepositoryInterface
{
    public function __construct(
        protected Book $model
    ) {}

    #[Override]
    public function all(): Collection
    {
        return $this->model->with(['user', 'files'])->latest()->get();
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

    #[Override]
    public function forUser(int $userId): Collection
    {
        return $this
            ->model
            ->where('user_id', $userId)
            ->with('files')
            ->latest()
            ->get();
    }
}
