<?php

namespace App\Repositories\Contracts;

use App\Models\Topic;
use Illuminate\Database\Eloquent\Collection;

interface TopicRepositoryInterface
{
    public function all(): Collection;

    public function find(int $id);

    public function create(array $data);

    public function update(Topic $topic, array $data);

    public function delete(Topic $topic);
}
