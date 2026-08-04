<?php

namespace App\Services;

use App\Models\TopicTest;
use App\Models\TopicTestOption;
use App\Repositories\Contracts\TopicTestRepositoryInterface;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class TopicTestService
{
    public function __construct(
        protected TopicTestRepositoryInterface $topicTestRepo
    ) {}

    /**
     * @param  array{q?: string}  $filters
     */
    public function myTests(int $userId, array $filters = []): Collection
    {
        return $this->topicTestRepo->forUser($userId, $filters);
    }

    /**
     * @param  array{q?: string, teacher_id?: int, page_name?: string}  $filters
     */
    public function all(array $filters = []): LengthAwarePaginator
    {
        return $this->topicTestRepo->all($filters);
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
        if ($topicTest->user_id !== auth()->id() && ! auth()->user()->hasRole('admin')) {
            throw new Exception('Bu test ustida amaliyot bajara olmaysiz', 403);
        }
    }

    /**
     * Bulk-inserts questions and their options in two queries total instead
     * of one per question/option — a DTM test with 90 questions previously
     * took 450+ individual inserts.
     *
     * @param  array<int, array{text: string, options: array<int, array{text: string}>, correct: int}>  $questions
     */
    protected function syncQuestions(TopicTest $topicTest, array $questions): void
    {
        if (empty($questions)) {
            return;
        }

        $now = now();

        $topicTest->questions()->insert(
            collect($questions)->map(fn (array $question, int $index) => [
                'topic_test_id' => $topicTest->id,
                'question' => $question['text'],
                'order' => $index,
                'created_at' => $now,
                'updated_at' => $now,
            ])->all()
        );

        $questionIds = $topicTest->questions()->orderBy('order')->pluck('id', 'order');

        $optionRows = [];
        foreach ($questions as $index => $question) {
            foreach ($question['options'] as $optionIndex => $option) {
                $optionRows[] = [
                    'topic_test_question_id' => $questionIds[$index],
                    'option_text' => $option['text'],
                    'is_correct' => (int) $question['correct'] === $optionIndex,
                    'order' => $optionIndex,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        TopicTestOption::insert($optionRows);
    }
}
