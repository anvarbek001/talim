<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface LessonRepositoryInterface
{
    public function all(): Collection;
    public function create(array $data);
    public function find(int $id);
    public function forUser(int $userId): Collection;
}
