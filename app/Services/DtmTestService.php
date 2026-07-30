<?php

namespace App\Services;

use App\Models\DtmTest;
use App\Models\Grade;
use App\Models\Science;
use App\Repositories\Contracts\DtmTestRepositoryInterface;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class DtmTestService
{
    public function __construct(
        protected DtmTestRepositoryInterface $dtmTestRepo
    ) {}

    public function myTests(int $userId): Collection
    {
        return $this->dtmTestRepo->forUser($userId);
    }

    public function create(array $data, int $userId): DtmTest
    {
        return DB::transaction(function () use ($data, $userId) {
            $dtmTest = $this->dtmTestRepo->create([
                'user_id' => $userId,
                'block1_science_id' => $data['block1_science_id'],
                'block2_science_id' => $data['block2_science_id'],
                'grade_id' => $this->defaultGradeId(),
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'duration_minutes' => $data['duration_minutes'],
            ]);

            $this->syncQuestions($dtmTest, $data['questions']);

            return $dtmTest;
        });
    }

    public function update(DtmTest $dtmTest, array $data): DtmTest
    {
        $this->authorize($dtmTest);

        return DB::transaction(function () use ($dtmTest, $data) {
            $this->dtmTestRepo->update($dtmTest, [
                'block1_science_id' => $data['block1_science_id'],
                'block2_science_id' => $data['block2_science_id'],
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'duration_minutes' => $data['duration_minutes'],
            ]);

            $dtmTest->questions()->delete();
            $this->syncQuestions($dtmTest, $data['questions']);

            return $dtmTest;
        });
    }

    public function delete(DtmTest $dtmTest): bool
    {
        $this->authorize($dtmTest);

        return $this->dtmTestRepo->delete($dtmTest);
    }

    /**
     * Resolve the science IDs backing block 3's mandatory subjects.
     *
     * @return array<string, int>
     */
    public function mandatoryScienceIds(): array
    {
        $sciences = Science::whereIn('title', DtmTest::MANDATORY_SUBJECTS)->pluck('id', 'title');

        $ids = [];
        foreach (DtmTest::MANDATORY_SUBJECTS as $slug => $title) {
            if (! isset($sciences[$title])) {
                throw new Exception("Majburiy fan topilmadi: {$title}");
            }

            $ids[$slug] = $sciences[$title];
        }

        return $ids;
    }

    protected function defaultGradeId(): int
    {
        return Grade::firstOrCreate(['title' => DtmTest::GRADE_TITLE])->id;
    }

    protected function authorize(DtmTest $dtmTest): void
    {
        if ($dtmTest->user_id !== auth()->id()) {
            throw new Exception('Bu test ustida amaliyot bajara olmaysiz', 403);
        }
    }

    /**
     * @param  array<int, array{block: int, science_id: int, text: string, options: array<int, array{text: string}>, correct: int}>  $questions
     */
    protected function syncQuestions(DtmTest $dtmTest, array $questions): void
    {
        foreach ($questions as $index => $question) {
            $questionModel = $dtmTest->questions()->create([
                'question' => $question['text'],
                'block' => $question['block'],
                'science_id' => $question['science_id'],
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
