<?php

namespace Database\Seeders;

use App\Models\DtmTest;
use App\Models\SertifikatTest;
use App\Models\TestAttempt;
use App\Models\TopicTest;
use App\Models\User;
use App\Services\StudentTestService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class TestAttemptSeeder extends Seeder
{
    /**
     * Only ever seed onto these specific demo accounts — never a generic
     * "any user with the student role" query, since a real person's account
     * can also hold that role and must not be polluted with fake attempts.
     *
     * @var array<int, string>
     */
    protected const DEMO_STUDENT_EMAILS = ['student@talim.test', 'student2@talim.test', 'student3@talim.test'];

    public function run(): void
    {
        $students = User::whereIn('email', self::DEMO_STUDENT_EMAILS)->orderBy('email')->get();

        if ($students->count() < 3) {
            return;
        }

        [$student1, $student2, $student3] = [$students[0], $students[1], $students[2]];

        $topicTest = TopicTest::where('title', 'Kvadrat tenglamalar — 1-nazorat')->first();
        $dtmTest = DtmTest::where('title', 'DTM — 2026, namunaviy variant')->first();
        $sertifikatTest = SertifikatTest::where('title', 'Ingliz tili — B1 sertifikat testi')->first();

        $service = app(StudentTestService::class);

        if ($topicTest) {
            $this->attemptMcqTest($service, 'topic', $topicTest, $student1, mostlyCorrect: true);
            $this->attemptMcqTest($service, 'topic', $topicTest, $student2, mostlyCorrect: false);
        }

        if ($dtmTest) {
            $this->attemptMcqTest($service, 'dtm', $dtmTest, $student1, mostlyCorrect: true);
            $this->attemptMcqTest($service, 'dtm', $dtmTest, $student3, mostlyCorrect: false);
        }

        if ($sertifikatTest) {
            $this->attemptSertifikatTest($service, $sertifikatTest, $student2);
        }
    }

    /**
     * @param  TopicTest|DtmTest  $testable
     */
    protected function attemptMcqTest(StudentTestService $service, string $type, $testable, User $student, bool $mostlyCorrect): void
    {
        if ($this->alreadySubmitted($student, $testable)) {
            return;
        }

        $attempt = $service->startAttempt($type, $testable, $student->id);
        $answers = $this->buildAnswers($testable->questions()->with('options')->get(), $mostlyCorrect);

        $service->submitAttempt($attempt, $student->id, $answers, []);
    }

    protected function attemptSertifikatTest(StudentTestService $service, SertifikatTest $testable, User $student): void
    {
        if ($this->alreadySubmitted($student, $testable)) {
            return;
        }

        $attempt = $service->startAttempt('sertifikat', $testable, $student->id);
        $answers = $this->buildAnswers($testable->questions()->with('options')->get(), mostlyCorrect: true);

        $writtenAnswers = $testable->writtenQuestions()->get()
            ->mapWithKeys(fn ($q) => [$q->id => 'Bu — namunaviy o\'quvchi javobi. O\'qituvchi tomonidan baholanishi kerak.'])
            ->all();

        $service->submitAttempt($attempt, $student->id, $answers, $writtenAnswers);
    }

    protected function alreadySubmitted(User $student, $testable): bool
    {
        return TestAttempt::where('user_id', $student->id)
            ->where('testable_type', $testable::class)
            ->where('testable_id', $testable->id)
            ->where('status', 'submitted')
            ->exists();
    }

    /**
     * @param  Collection<int, Model>  $questions
     * @return array<int, int> questionId => selectedOptionId
     */
    protected function buildAnswers(Collection $questions, bool $mostlyCorrect): array
    {
        $answers = [];

        foreach ($questions as $index => $question) {
            $wantCorrect = $mostlyCorrect ? $index % 4 !== 3 : $index % 3 === 0;
            $option = $wantCorrect
                ? $question->options->firstWhere('is_correct', true)
                : $question->options->firstWhere('is_correct', false);

            if ($option) {
                $answers[$question->id] = $option->id;
            }
        }

        return $answers;
    }
}
