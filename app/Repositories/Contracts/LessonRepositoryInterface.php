<?php

namespace App\Repositories\Contracts;

use App\Models\Lesson;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface LessonRepositoryInterface
{
    /**
     * @param  array{q?: string, teacher_id?: int, science_id?: int, per_page?: int}  $filters
     */
    public function all(array $filters = []): LengthAwarePaginator;

    public function create(array $data);

    public function find(int $id);

    /**
     * @param  array{q?: string, per_page?: int}  $filters
     */
    public function forUser(int $userId, array $filters = []): LengthAwarePaginator;

    public function update(Lesson $lesson, array $data): Lesson;

    public function delete(Lesson $lesson): bool;
}
