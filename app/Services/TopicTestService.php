<?php

namespace App\Services;

use App\Models\TopicTest;
use App\Repositories\Contracts\TopicTestRepositoryInterface;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class TopicTestService
{
    public function __construct(
        protected TopicTestRepositoryInterface $topicTestRepo
    ) {}

    public function myTests(int $userId): Collection
    {
        return $this->topicTestRepo->forUser($userId);
    }

    public function create(array $data, int $userId): TopicTest
    {
        return DB::transaction(function () use ($data, $userId) {
            $topicTest = $this->topicTestRepo->create([
                'user_id' => $userId,
                'science_id' => $data['science_id'],
                'grade_id' => $data['grade_id'],
                'section_id' => $data['section_id'],
                'topic_id' => $data['topic_id'],
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'duration_minutes' => $data['duration_minutes'],
            ]);

            $this->syncQuestions($topicTest, $data['questions']);

            return $topicTest;
        });
    }

    public function update(TopicTest $topicTest, array $data): TopicTest
    {
        $this->authorize($topicTest);

        return DB::transaction(function () use ($topicTest, $data) {
            $this->topicTestRepo->update($topicTest, [
                'science_id' => $data['science_id'],
                'grade_id' => $data['grade_id'],
                'section_id' => $data['section_id'],
                'topic_id' => $data['topic_id'],
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'duration_minutes' => $data['duration_minutes'],
            ]);

            $topicTest->questions()->delete();
            $this->syncQuestions($topicTest, $data['questions']);

            return $topicTest;
        });
    }

    public function delete(TopicTest $topicTest): bool
    {
        $this->authorize($topicTest);

        return $this->topicTestRepo->delete($topicTest);
    }

    protected function authorize(TopicTest $topicTest): void
    {
        if ($topicTest->user_id !== auth()->id()) {
            throw new Exception('Bu test ustida amaliyot bajara olmaysiz', 403);
        }
    }

    /**
     * @param  array<int, array{text: string, options: array<int, array{text: string}>, correct: int}>  $questions
     */
    protected function syncQuestions(TopicTest $topicTest, array $questions): void
    {
        foreach ($questions as $index => $question) {
            $questionModel = $topicTest->questions()->create([
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
}
