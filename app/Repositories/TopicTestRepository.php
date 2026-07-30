<?php

namespace App\Repositories;

use App\Models\TopicTest;
use App\Repositories\Contracts\TopicTestRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Override;

class TopicTestRepository implements TopicTestRepositoryInterface
{
    public function __construct(
        protected TopicTest $model
    ) {}

    #[Override]
    public function forUser(int $userId): Collection
    {
        return $this
            ->model
            ->where('user_id', $userId)
            ->with(['science', 'grade', 'section', 'topic', 'questions.options'])
            ->latest()
            ->get();
    }

    #[Override]
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    #[Override]
    public function update(TopicTest $topicTest, array $data)
    {
        $topicTest->update($data);

        return $topicTest;
    }

    #[Override]
    public function delete(TopicTest $topicTest)
    {
        return $topicTest->delete();
    }
}
