<?php

namespace App\Services;

use App\Models\SertifikatTest;
use App\Models\SertifikatTestOption;
use App\Repositories\Contracts\SertifikatTestRepositoryInterface;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class SertifikatTestService
{
    public function __construct(
        protected SertifikatTestRepositoryInterface $sertifikatTestRepo
    ) {}

    /**
     * @param  array{q?: string}  $filters
     */
    public function myTests(int $userId, array $filters = []): Collection
    {
        return $this->sertifikatTestRepo->forUser($userId, $filters);
    }

    /**
     * @param  array{q?: string, teacher_id?: int, page_name?: string}  $filters
     */
    public function all(array $filters = []): LengthAwarePaginator
    {
        return $this->sertifikatTestRepo->all($filters);
    }

    public function create(array $data, int $userId): SertifikatTest
    {
        return DB::transaction(function () use ($data, $userId) {
            $sertifikatTest = $this->sertifikatTestRepo->create([
                'user_id' => $userId,
                'science_id' => $data['science_id'],
                'level' => $data['level'] ?? null,
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'duration_minutes' => $data['duration_minutes'],
                'price' => $data['price'] ?? 0,
            ]);

            $this->syncQuestions($sertifikatTest, $data['questions']);
            $this->syncWrittenQuestions($sertifikatTest, $data['written_questions'] ?? []);

            return $sertifikatTest;
        });
    }

    public function update(SertifikatTest $sertifikatTest, array $data): SertifikatTest
    {
        $this->authorize($sertifikatTest);

        return DB::transaction(function () use ($sertifikatTest, $data) {
            $this->sertifikatTestRepo->update($sertifikatTest, [
                'science_id' => $data['science_id'],
                'level' => $data['level'] ?? null,
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'duration_minutes' => $data['duration_minutes'],
                'price' => $data['price'] ?? 0,
            ]);

            $sertifikatTest->questions()->delete();
            $this->syncQuestions($sertifikatTest, $data['questions']);

            $sertifikatTest->writtenQuestions()->delete();
            $this->syncWrittenQuestions($sertifikatTest, $data['written_questions'] ?? []);

            return $sertifikatTest;
        });
    }

    public function delete(SertifikatTest $sertifikatTest): bool
    {
        $this->authorize($sertifikatTest);

        return $this->sertifikatTestRepo->delete($sertifikatTest);
    }

    protected function authorize(SertifikatTest $sertifikatTest): void
    {
        if ($sertifikatTest->user_id !== auth()->id() && ! auth()->user()->hasRole('admin')) {
            throw new Exception('Bu test ustida amaliyot bajara olmaysiz', 403);
        }
    }

    /**
     * Bulk-inserts questions and their options in two queries total instead
     * of one per question/option.
     *
     * @param  array<int, array{text: string, options: array<int, array{text: string}>, correct: int}>  $questions
     */
    protected function syncQuestions(SertifikatTest $sertifikatTest, array $questions): void
    {
        if (empty($questions)) {
            return;
        }

        $now = now();

        $sertifikatTest->questions()->insert(
            collect($questions)->map(fn (array $question, int $index) => [
                'sertifikat_test_id' => $sertifikatTest->id,
                'question' => $question['text'],
                'order' => $index,
                'created_at' => $now,
                'updated_at' => $now,
            ])->all()
        );

        $questionIds = $sertifikatTest->questions()->orderBy('order')->pluck('id', 'order');

        $optionRows = [];
        foreach ($questions as $index => $question) {
            foreach ($question['options'] as $optionIndex => $option) {
                $optionRows[] = [
                    'sertifikat_test_question_id' => $questionIds[$index],
                    'option_text' => $option['text'],
                    'is_correct' => (int) $question['correct'] === $optionIndex,
                    'order' => $optionIndex,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        SertifikatTestOption::insert($optionRows);
    }

    /**
     * @param  array<int, array{text: string, max_score?: int}>  $writtenQuestions
     */
    protected function syncWrittenQuestions(SertifikatTest $sertifikatTest, array $writtenQuestions): void
    {
        if (empty($writtenQuestions)) {
            return;
        }

        $now = now();

        $sertifikatTest->writtenQuestions()->insert(
            collect($writtenQuestions)->map(fn (array $writtenQuestion, int $index) => [
                'sertifikat_test_id' => $sertifikatTest->id,
                'question' => $writtenQuestion['text'],
                'max_score' => $writtenQuestion['max_score'] ?? 10,
                'order' => $index,
                'created_at' => $now,
                'updated_at' => $now,
            ])->all()
        );
    }
}
