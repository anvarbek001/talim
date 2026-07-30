<?php

namespace App\Repositories\Contracts;

use App\Models\TopicTest;
use Illuminate\Database\Eloquent\Collection;

interface TopicTestRepositoryInterface
{
    public function forUser(int $userId): Collection;

    public function create(array $data);

    public function update(TopicTest $topicTest, array $data);

    public function delete(TopicTest $topicTest);
}
