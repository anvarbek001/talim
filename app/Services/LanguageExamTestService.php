<?php

namespace App\Services;

use App\Models\LanguageExamTest;
use App\Models\LanguageExamTestOption;
use App\Repositories\Contracts\LanguageExamTestRepositoryInterface;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class LanguageExamTestService
{
    public function __construct(
        protected LanguageExamTestRepositoryInterface $languageExamTestRepo
    ) {}

    /**
     * @param  array{q?: string}  $filters
     */
    public function myTests(int $userId, array $filters = []): Collection
    {
        return $this->languageExamTestRepo->forUser($userId, $filters);
    }

    /**
     * @param  array{q?: string, teacher_id?: int, page_name?: string}  $filters
     */
    public function all(array $filters = []): LengthAwarePaginator
    {
        return $this->languageExamTestRepo->all($filters);
    }

    public function create(array $data, int $userId): LanguageExamTest
    {
        return DB::transaction(function () use ($data, $userId) {
            $languageExamTest = $this->languageExamTestRepo->create([
                'user_id' => $userId,
                'science_id' => $data['science_id'],
                'exam_type' => $data['exam_type'],
                'level' => $data['level'] ?? null,
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'duration_minutes' => $data['duration_minutes'],
                'price' => $data['price'] ?? 0,
            ]);

            $this->syncQuestions($languageExamTest, $data['questions']);
            $this->syncWrittenQuestions($languageExamTest, $data['written_questions'] ?? []);

            return $languageExamTest;
        });
    }

    public function update(LanguageExamTest $languageExamTest, array $data): LanguageExamTest
    {
        $this->authorize($languageExamTest);

        return DB::transaction(function () use ($languageExamTest, $data) {
            $this->languageExamTestRepo->update($languageExamTest, [
                'science_id' => $data['science_id'],
                'exam_type' => $data['exam_type'],
                'level' => $data['level'] ?? null,
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'duration_minutes' => $data['duration_minutes'],
                'price' => $data['price'] ?? 0,
            ]);

            $languageExamTest->questions()->delete();
            $this->syncQuestions($languageExamTest, $data['questions']);

            $languageExamTest->writtenQuestions()->delete();
            $this->syncWrittenQuestions($languageExamTest, $data['written_questions'] ?? []);

            return $languageExamTest;
        });
    }

    public function delete(LanguageExamTest $languageExamTest): bool
    {
        $this->authorize($languageExamTest);

        return $this->languageExamTestRepo->delete($languageExamTest);
    }

    protected function authorize(LanguageExamTest $languageExamTest): void
    {
        if ($languageExamTest->user_id !== auth()->id() && ! auth()->user()->hasRole('admin')) {
            throw new Exception('Bu test ustida amaliyot bajara olmaysiz', 403);
        }
    }

    /**
     * Bulk-inserts questions and their options in two queries total instead
     * of one per question/option.
     *
     * @param  array<int, array{text: string, options: array<int, array{text: string}>, correct: int}>  $questions
     */
    protected function syncQuestions(LanguageExamTest $languageExamTest, array $questions): void
    {
        if (empty($questions)) {
            return;
        }

        $now = now();

        $languageExamTest->questions()->insert(
            collect($questions)->map(fn (array $question, int $index) => [
                'language_exam_test_id' => $languageExamTest->id,
                'question' => $question['text'],
                'order' => $index,
                'created_at' => $now,
                'updated_at' => $now,
            ])->all()
        );

        $questionIds = $languageExamTest->questions()->orderBy('order')->pluck('id', 'order');

        $optionRows = [];
        foreach ($questions as $index => $question) {
            foreach ($question['options'] as $optionIndex => $option) {
                $optionRows[] = [
                    'language_exam_test_question_id' => $questionIds[$index],
                    'option_text' => $option['text'],
                    'is_correct' => (int) $question['correct'] === $optionIndex,
                    'order' => $optionIndex,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        LanguageExamTestOption::insert($optionRows);
    }

    /**
     * @param  array<int, array{text: string, max_score?: int}>  $writtenQuestions
     */
    protected function syncWrittenQuestions(LanguageExamTest $languageExamTest, array $writtenQuestions): void
    {
        if (empty($writtenQuestions)) {
            return;
        }

        $now = now();

        $languageExamTest->writtenQuestions()->insert(
            collect($writtenQuestions)->map(fn (array $writtenQuestion, int $index) => [
                'language_exam_test_id' => $languageExamTest->id,
                'question' => $writtenQuestion['text'],
                'max_score' => $writtenQuestion['max_score'] ?? 10,
                'order' => $index,
                'created_at' => $now,
                'updated_at' => $now,
            ])->all()
        );
    }
}
