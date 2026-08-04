<?php

namespace App\Http\Controllers;

use App\Http\Requests\DtmTestRequest;
use App\Models\DtmTest;
use App\Services\DtmTestService;
use App\Services\TestQuestionFileParser;
use Exception;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class DtmTestController extends Controller implements HasMiddleware
{
    public function __construct(
        protected DtmTestService $dtmTestServ,
        protected TestQuestionFileParser $fileParser,
    ) {}

    public static function middleware(): array
    {
        return ['auth'];
    }

    public function store(DtmTestRequest $request)
    {
        $data = $this->resolveQuestions($request, $request->validated());
        $this->dtmTestServ->create($data, Auth::id());

        return redirect()->route('tests.index')->with('success', 'DTM testi muvaffaqiyatli yaratildi');
    }

    public function update(DtmTestRequest $request, DtmTest $dtmTest)
    {
        $data = $this->resolveQuestions($request, $request->validated());

        try {
            $this->dtmTestServ->update($dtmTest, $data);
        } catch (Exception $e) {
            return redirect()->route('tests.index')->with('error', $e->getMessage());
        }

        return redirect()->route('tests.index')->with('success', 'DTM testi tahrirlandi');
    }

    public function destroy(DtmTest $dtmTest)
    {
        try {
            $this->dtmTestServ->delete($dtmTest);
        } catch (Exception $e) {
            return redirect()->route('tests.index')->with('error', $e->getMessage());
        }

        return redirect()->route('tests.index')->with('success', "DTM testi o'chirildi");
    }

    /**
     * Normalize block1/block2/mandatory-subject questions — whether typed
     * manually or uploaded as separate Excel files — into the flat
     * {block, science_id, text, options, correct} shape DtmTestService expects.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function resolveQuestions(DtmTestRequest $request, array $data): array
    {
        $mandatoryScienceIds = $this->dtmTestServ->mandatoryScienceIds();

        $data['questions'] = $request->hasFile('block1_questions_file')
            ? $this->resolveQuestionsFromExcel($request, $data, $mandatoryScienceIds)
            : $this->resolveManualQuestions($data, $mandatoryScienceIds);

        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  array<string, int>  $mandatoryScienceIds
     * @return array<int, array{block: int, science_id: int, text: string, options: array<int, array{text: string}>, correct: int}>
     */
    protected function resolveManualQuestions(array $data, array $mandatoryScienceIds): array
    {
        return collect($data['questions'])->map(function (array $question) use ($data, $mandatoryScienceIds) {
            $block = (int) $question['block'];

            $scienceId = match ($block) {
                1 => $data['block1_science_id'],
                2 => $data['block2_science_id'],
                default => $mandatoryScienceIds[$question['subject']],
            };

            return [
                'block' => $block,
                'science_id' => $scienceId,
                'text' => $question['text'],
                'options' => $question['options'],
                'correct' => $question['correct'],
            ];
        })->all();
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  array<string, int>  $mandatoryScienceIds
     * @return array<int, array{block: int, science_id: int, text: string, options: array<int, array{text: string}>, correct: int}>
     */
    protected function resolveQuestionsFromExcel(DtmTestRequest $request, array $data, array $mandatoryScienceIds): array
    {
        $slots = [
            ['file' => 'block1_questions_file', 'block' => 1, 'science_id' => $data['block1_science_id'], 'count' => DtmTest::BLOCK1_QUESTION_COUNT, 'label' => '1-blok'],
            ['file' => 'block2_questions_file', 'block' => 2, 'science_id' => $data['block2_science_id'], 'count' => DtmTest::BLOCK2_QUESTION_COUNT, 'label' => '2-blok'],
            ['file' => 'block3_ona_tili_questions_file', 'block' => 3, 'science_id' => $mandatoryScienceIds['ona_tili'], 'count' => DtmTest::BLOCK3_SUBJECT_QUESTION_COUNT, 'label' => 'Ona tili'],
            ['file' => 'block3_matematika_questions_file', 'block' => 3, 'science_id' => $mandatoryScienceIds['matematika'], 'count' => DtmTest::BLOCK3_SUBJECT_QUESTION_COUNT, 'label' => 'Matematika'],
            ['file' => 'block3_tarix_questions_file', 'block' => 3, 'science_id' => $mandatoryScienceIds['tarix'], 'count' => DtmTest::BLOCK3_SUBJECT_QUESTION_COUNT, 'label' => 'Tarix'],
        ];

        $questions = [];

        foreach ($slots as $slot) {
            $parsed = $this->fileParser->parse($request->file($slot['file']));

            if (count($parsed) !== $slot['count']) {
                throw ValidationException::withMessages([
                    $slot['file'] => "{$slot['label']} fayli uchun aniq {$slot['count']} ta savol bo'lishi kerak. Faylda: ".count($parsed).' ta.',
                ]);
            }

            foreach ($parsed as $question) {
                $questions[] = [
                    'block' => $slot['block'],
                    'science_id' => $slot['science_id'],
                    'text' => $question['text'],
                    'options' => $question['options'],
                    'correct' => $question['correct'],
                ];
            }
        }

        return $questions;
    }
}
