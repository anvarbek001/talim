<?php

namespace App\Repositories;

use App\Models\Lesson;
use App\Repositories\Contracts\LessonRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Override;

class LessonRepository implements LessonRepositoryInterface
{
    public function __construct(
        protected Lesson $model
    ) {}

    #[Override]
    public function all(): Collection
    {
        return $this->model->with(['user', 'science', 'grade', 'section', 'topic'])->get();
    }

    #[Override]
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    #[Override]
    public function find(int $id)
    {
        return $this->model->where('id', $id)->with(['user', 'science', 'grade', 'section', 'topic', 'lessonfiles'])->first();
    }

    #[Override]
    public function forUser(int $userId): Collection
    {
        return $this->model
            ->where('user_id', $userId)
            ->with(['science', 'grade', 'section', 'topic', 'lessonfiles'])
            ->latest()
            ->get();
    }
}
