<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface SectionRepositoryInterface
{
    public function all(): Collection;
    public function create(array $data);
    public function find(int $id);
}
