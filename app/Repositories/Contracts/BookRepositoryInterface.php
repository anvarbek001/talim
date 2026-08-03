<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface BookRepositoryInterface
{
    public function all(): Collection;

    public function create(array $data);

    public function find(int $id);

    public function forUser(int $userId): Collection;
}
