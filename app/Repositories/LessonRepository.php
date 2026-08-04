<?php

namespace App\Repositories;

use App\Models\Lesson;
use App\Repositories\Contracts\LessonRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Override;

class LessonRepository implements LessonRepositoryInterface
{
    public function __construct(
        protected Lesson $model
    ) {}

    /**
     * @param  array{q?: string, teacher_id?: int, science_id?: int, per_page?: int}  $filters
     */
    #[Override]
    public function all(array $filters = []): LengthAwarePaginator
    {
        return $this->model
            ->with(['user', 'science', 'grade', 'section', 'topic'])
            ->when($filters['q'] ?? null, fn ($query, $search) => $query->where('title', 'like', "%{$search}%"))
            ->when($filters['teacher_id'] ?? null, fn ($query, $teacherId) => $query->where('user_id', $teacherId))
            ->when($filters['science_id'] ?? null, fn ($query, $scienceId) => $query->where('science_id', $scienceId))
            ->latest()
            ->paginate($filters['per_page'] ?? 20)
            ->withQueryString();
    }

    #[Override]
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    #[Override]
    public function find(int $id)
    {
        return $this->model->where(['user_id' => Auth::id(), 'id' => $id])->with(['user', 'science', 'grade', 'section', 'topic', 'lessonfiles'])->first();
    }

    /**
     * @param  array{q?: string, per_page?: int}  $filters
     */
    #[Override]
    public function forUser(int $userId, array $filters = []): LengthAwarePaginator
    {
        return $this
            ->model
            ->where('user_id', $userId)
            ->with(['science', 'grade', 'section', 'topic', 'lessonfiles'])
            ->when($filters['q'] ?? null, fn ($query, $search) => $query->where('title', 'like', "%{$search}%"))
            ->latest()
            ->paginate($filters['per_page'] ?? 12)
            ->withQueryString();
    }

    #[Override]
    public function update(Lesson $lesson, array $data): Lesson
    {
        $lesson->update($data);

        return $lesson;
    }

    #[Override]
    public function delete(Lesson $lesson): bool
    {
        return $lesson->delete();
    }
}
