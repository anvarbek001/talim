<?php

namespace App\Services;

use App\Models\DtmTest;
use App\Models\DtmTestQuestion;
use App\Models\LanguageExamTest;
use App\Models\LanguageExamTestWrittenQuestion;
use App\Models\SertifikatTest;
use App\Models\SertifikatTestWrittenQuestion;
use App\Models\TestAttempt;
use App\Models\TestAttemptAnswer;
use App\Models\TopicTest;
use Exception;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class StudentTestService
{
    /** @var array<string, class-string<Model>> */
    protected const TYPE_MAP = [
        'topic' => TopicTest::class,
        'dtm' => DtmTest::class,
        'sertifikat' => SertifikatTest::class,
        'language_exam' => LanguageExamTest::class,
    ];

    /**
     * Maps a written-question model class to the relation name it exposes
     * back to its parent test, used to generalize grading across test types
     * that support essay/written questions.
     *
     * @var array<class-string<Model>, string>
     */
    protected const WRITTEN_QUESTION_RELATIONS = [
        SertifikatTestWrittenQuestion::class => 'sertifikatTest',
        LanguageExamTestWrittenQuestion::class => 'languageExamTest',
    ];

    public function catalog(int $userId): Collection
    {
        $purchasesFor = fn ($query) => $query->with(['purchases' => fn ($q) => $q->where('user_id', $userId)]);

        // "purchased" badge means *currently active*, not "ever bought" —
        // a section's purchase can have expired (1 oylik obuna) since.
        $isActive = fn ($purchases) => $purchases->contains(
            fn (\App\Models\Purchase $p) => $p->expires_at === null || $p->expires_at->isFuture()
        );

        $topicTests = TopicTest::with([
            'science', 'grade', 'topic', 'questions',
            'section' => fn ($q) => $q->with(['purchases' => fn ($pq) => $pq->where('user_id', $userId)]),
        ])->get()
            ->map(fn (TopicTest $t) => [
                'type' => 'topic',
                'id' => $t->id,
                'title' => $t->title,
                'science' => $t->science,
                'extra' => $t->section->title.' | '.$t->topic->title,
                'questions_count' => $t->questions->count(),
                'has_written' => false,
                'duration_minutes' => $t->duration_minutes,
                'price' => $t->section->price,
                'purchased' => $isActive($t->section->purchases),
                'purchase_type' => 'section',
                'purchase_id' => $t->section_id,
                'created_at' => $t->created_at,
            ]);

        $dtmTests = $purchasesFor(DtmTest::with(['block1Science', 'block2Science', 'questions']))->get()
            ->map(fn (DtmTest $t) => [
                'type' => 'dtm',
                'id' => $t->id,
                'title' => $t->title,
                'science' => $t->block1Science,
                'extra' => '2-blok: '.$t->block2Science->title,
                'questions_count' => $t->questions->count(),
                'has_written' => false,
                'duration_minutes' => $t->duration_minutes,
                'price' => $t->price,
                'purchased' => $isActive($t->purchases),
                'purchase_type' => 'dtm',
                'purchase_id' => $t->id,
                'created_at' => $t->created_at,
            ]);

        $sertifikatTests = $purchasesFor(SertifikatTest::with(['science', 'questions', 'writtenQuestions']))->get()
            ->map(fn (SertifikatTest $t) => [
                'type' => 'sertifikat',
                'id' => $t->id,
                'title' => $t->title,
                'science' => $t->science,
                'extra' => $t->level ?? '—',
                'questions_count' => $t->questions->count(),
                'has_written' => $t->writtenQuestions->isNotEmpty(),
                'duration_minutes' => $t->duration_minutes,
                'price' => $t->price,
                'purchased' => $isActive($t->purchases),
                'purchase_type' => 'sertifikat',
                'purchase_id' => $t->id,
                'created_at' => $t->created_at,
            ]);

        $languageExamTests = $purchasesFor(LanguageExamTest::with(['science', 'questions', 'writtenQuestions']))->get()
            ->map(fn (LanguageExamTest $t) => [
                'type' => 'language_exam',
                'id' => $t->id,
                'title' => $t->title,
                'science' => $t->science,
                'extra' => $t->examTypeLabel().($t->level ? ' | '.$t->level : ''),
                'questions_count' => $t->questions->count(),
                'has_written' => $t->writtenQuestions->isNotEmpty(),
                'duration_minutes' => $t->duration_minutes,
                'price' => $t->price,
                'purchased' => $isActive($t->purchases),
                'purchase_type' => 'language_exam',
                'purchase_id' => $t->id,
                'created_at' => $t->created_at,
            ]);

        return collect()
            ->concat($topicTests)
            ->concat($dtmTests)
            ->concat($sertifikatTests)
            ->concat($languageExamTests)
            ->sortByDesc('created_at')
            ->values();
    }

    public function myAttempts(int $userId): EloquentCollection
    {
        return TestAttempt::where('user_id', $userId)
            ->with('testable')
            ->latest()
            ->get();
    }

    public function resolveTestable(string $type, int $id): Model
    {
        $class = self::TYPE_MAP[$type] ?? null;

        if ($class === null) {
            throw new Exception('Noto\'g\'ri test turi', 404);
        }

        $query = $class::with(['questions.options']);

        if ($class === DtmTest::class) {
            $query->with(['block1Science', 'block2Science']);
        } else {
            $query->with('science');
        }

        if ($class === SertifikatTest::class || $class === LanguageExamTest::class) {
            $query->with('writtenQuestions');
        }

        if ($class === TopicTest::class) {
            $query->with('section');
        }

        return $query->findOrFail($id);
    }

    public function startAttempt(string $type, Model $testable, int $userId): TestAttempt
    {
        $existing = TestAttempt::where('user_id', $userId)
            ->where('testable_type', $testable::class)
            ->where('testable_id', $testable->id)
            ->where('status', 'in_progress')
            ->first();

        if ($existing) {
            return $existing;
        }

        return TestAttempt::create([
            'user_id' => $userId,
            'testable_type' => $testable::class,
            'testable_id' => $testable->id,
            'status' => 'in_progress',
            'started_at' => now(),
        ]);
    }

    public function attemptForTaking(TestAttempt $attempt, int $userId): TestAttempt
    {
        $this->authorizeAttempt($attempt, $userId);

        if (! $attempt->isInProgress()) {
            throw new Exception('Bu urinish allaqachon yakunlangan', 409);
        }

        $attempt->load(['testable.questions.options', 'testable.user']);

        if ($attempt->testable instanceof SertifikatTest || $attempt->testable instanceof LanguageExamTest) {
            $attempt->testable->load('writtenQuestions');
        }

        return $attempt;
    }

    /**
     * @param  array<int|string, int|string>  $mcqAnswers  questionId => selectedOptionId
     * @param  array<int|string, string>  $writtenAnswers  writtenQuestionId => answer text
     */
    public function submitAttempt(TestAttempt $attempt, int $userId, array $mcqAnswers, array $writtenAnswers): TestAttempt
    {
        $this->authorizeAttempt($attempt, $userId);

        if (! $attempt->isInProgress()) {
            throw new Exception('Bu urinish allaqachon yakunlangan', 409);
        }

        return DB::transaction(function () use ($attempt, $mcqAnswers, $writtenAnswers) {
            $testable = $attempt->testable()->with('questions.options')->first();

            foreach ($testable->questions as $question) {
                $selectedOptionId = $mcqAnswers[$question->id] ?? null;
                $selectedOption = $selectedOptionId
                    ? $question->options->firstWhere('id', (int) $selectedOptionId)
                    : null;

                $weight = $question instanceof DtmTestQuestion ? $question->weight() : 1;

                TestAttemptAnswer::create([
                    'test_attempt_id' => $attempt->id,
                    'questionable_type' => $question::class,
                    'questionable_id' => $question->id,
                    'selected_option_id' => $selectedOption?->id,
                    'score' => $selectedOption && $selectedOption->is_correct ? $weight : 0,
                    'max_score' => $weight,
                    'graded_at' => now(),
                ]);
            }

            if ($testable instanceof SertifikatTest || $testable instanceof LanguageExamTest) {
                foreach ($testable->writtenQuestions as $writtenQuestion) {
                    $text = trim((string) ($writtenAnswers[$writtenQuestion->id] ?? ''));

                    TestAttemptAnswer::create([
                        'test_attempt_id' => $attempt->id,
                        'questionable_type' => $writtenQuestion::class,
                        'questionable_id' => $writtenQuestion->id,
                        'answer_text' => $text !== '' ? $text : null,
                        'score' => null,
                        'max_score' => $writtenQuestion->max_score,
                        'graded_at' => null,
                    ]);
                }
            }

            $attempt->update([
                'status' => 'submitted',
                'submitted_at' => now(),
            ]);

            $this->recalculateScore($attempt);

            return $attempt->fresh(['answers', 'testable']);
        });
    }

    public function result(TestAttempt $attempt, int $userId): TestAttempt
    {
        $this->authorizeAttempt($attempt, $userId);

        return $attempt->load(['answers.questionable', 'testable']);
    }

    public function pendingWrittenAnswers(int $teacherId): EloquentCollection
    {
        return TestAttemptAnswer::whereHasMorph(
            'questionable',
            array_keys(self::WRITTEN_QUESTION_RELATIONS),
            function ($query, $type) use ($teacherId) {
                $relation = self::WRITTEN_QUESTION_RELATIONS[$type];
                $query->whereHas($relation, fn ($q) => $q->where('user_id', $teacherId));
            }
        )
            ->whereNull('graded_at')
            ->with(['attempt.user'])
            ->with(['questionable' => function ($morphTo) {
                $morphTo->morphWith(collect(self::WRITTEN_QUESTION_RELATIONS)
                    ->mapWithKeys(fn ($relation, $type) => [$type => [$relation]])
                    ->all());
            }])
            ->latest()
            ->get();
    }

    public function gradeWrittenAnswer(TestAttemptAnswer $answer, int $score, int $teacherId): TestAttemptAnswer
    {
        $relation = self::WRITTEN_QUESTION_RELATIONS[$answer->questionable_type] ?? null;

        if ($relation === null) {
            throw new Exception('Bu javob yozma savolga tegishli emas', 422);
        }

        $writtenQuestion = $answer->questionable()->with($relation)->first();

        if (! $writtenQuestion || $writtenQuestion->{$relation}->user_id !== $teacherId) {
            throw new Exception('Bu javobni baholay olmaysiz', 403);
        }

        $answer->update([
            'score' => max(0, min($score, $answer->max_score)),
            'graded_at' => now(),
        ]);

        $this->recalculateScore($answer->attempt);

        return $answer;
    }

    protected function authorizeAttempt(TestAttempt $attempt, int $userId): void
    {
        if ($attempt->user_id !== $userId) {
            throw new Exception('Bu urinishga kira olmaysiz', 403);
        }
    }

    protected function recalculateScore(TestAttempt $attempt): void
    {
        $answers = $attempt->answers()->get();

        $attempt->update([
            'score' => round((float) $answers->sum(fn (TestAttemptAnswer $a) => $a->score ?? 0), 2),
            'max_score' => round((float) $answers->sum('max_score'), 2),
        ]);
    }
}
