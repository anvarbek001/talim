<?php

namespace App\Repositories\Contracts;

use App\Models\LanguageExamTest;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface LanguageExamTestRepositoryInterface
{
    /**
     * @param  array{q?: string, teacher_id?: int, per_page?: int, page_name?: string}  $filters
     */
    public function all(array $filters = []): LengthAwarePaginator;

    /**
     * @param  array{q?: string}  $filters
     */
    public function forUser(int $userId, array $filters = []): Collection;

    public function create(array $data);

    public function update(LanguageExamTest $languageExamTest, array $data);

    public function delete(LanguageExamTest $languageExamTest);
}
