<?php

namespace App\Repositories;

use App\Models\LanguageExamTest;
use App\Repositories\Contracts\LanguageExamTestRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Override;

class LanguageExamTestRepository implements LanguageExamTestRepositoryInterface
{
    public function __construct(
        protected LanguageExamTest $model
    ) {}

    /**
     * @param  array{q?: string, teacher_id?: int, per_page?: int, page_name?: string}  $filters
     */
    #[Override]
    public function all(array $filters = []): LengthAwarePaginator
    {
        return $this->model
            ->with(['user', 'science'])
            ->when($filters['q'] ?? null, fn ($query, $search) => $query->where('title', 'like', "%{$search}%"))
            ->when($filters['teacher_id'] ?? null, fn ($query, $teacherId) => $query->where('user_id', $teacherId))
            ->latest()
            ->paginate($filters['per_page'] ?? 10, ['*'], $filters['page_name'] ?? 'page')
            ->withQueryString();
    }

    /**
     * @param  array{q?: string}  $filters
     */
    #[Override]
    public function forUser(int $userId, array $filters = []): Collection
    {
        return $this
            ->model
            ->where('user_id', $userId)
            ->with(['science', 'questions.options', 'writtenQuestions'])
            ->when($filters['q'] ?? null, fn ($query, $search) => $query->where('title', 'like', "%{$search}%"))
            ->latest()
            ->get();
    }

    #[Override]
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    #[Override]
    public function update(LanguageExamTest $languageExamTest, array $data)
    {
        $languageExamTest->update($data);

        return $languageExamTest;
    }

    #[Override]
    public function delete(LanguageExamTest $languageExamTest)
    {
        return $languageExamTest->delete();
    }
}
