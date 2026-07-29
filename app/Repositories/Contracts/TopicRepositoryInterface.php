<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface TopicRepositoryInterface
{
    public function all(): Collection;
    public function create(array $data);
}
