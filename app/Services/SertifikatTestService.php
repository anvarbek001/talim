<?php

namespace App\Services;

use App\Models\SertifikatTest;
use App\Repositories\Contracts\SertifikatTestRepositoryInterface;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class SertifikatTestService
{
    public function __construct(
        protected SertifikatTestRepositoryInterface $sertifikatTestRepo
    ) {}

    public function myTests(int $userId): Collection
    {
        return $this->sertifikatTestRepo->forUser($userId);
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
        if ($sertifikatTest->user_id !== auth()->id()) {
            throw new Exception('Bu test ustida amaliyot bajara olmaysiz', 403);
        }
    }

    /**
     * @param  array<int, array{text: string, options: array<int, array{text: string}>, correct: int}>  $questions
     */
    protected function syncQuestions(SertifikatTest $sertifikatTest, array $questions): void
    {
        foreach ($questions as $index => $question) {
            $questionModel = $sertifikatTest->questions()->create([
                'question' => $question['text'],
                'order' => $index,
            ]);

            foreach ($question['options'] as $optionIndex => $option) {
                $questionModel->options()->create([
                    'option_text' => $option['text'],
                    'is_correct' => (int) $question['correct'] === $optionIndex,
                    'order' => $optionIndex,
                ]);
            }
        }
    }

    /**
     * @param  array<int, array{text: string, max_score?: int}>  $writtenQuestions
     */
    protected function syncWrittenQuestions(SertifikatTest $sertifikatTest, array $writtenQuestions): void
    {
        foreach ($writtenQuestions as $index => $writtenQuestion) {
            $sertifikatTest->writtenQuestions()->create([
                'question' => $writtenQuestion['text'],
                'max_score' => $writtenQuestion['max_score'] ?? 10,
                'order' => $index,
            ]);
        }
    }
}
