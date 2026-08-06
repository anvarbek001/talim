<?php

namespace App\Services;

use App\Models\DtmTest;
use App\Models\LanguageExamTest;
use App\Models\SertifikatTest;
use App\Models\TestAttempt;
use App\Models\TopicTest;
use App\Models\User;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class TeacherStudentService
{
    /**
     * Every student who has submitted an attempt on one of this teacher's
     * tests, grouped with their attempt history and average score.
     *
     * @return Collection<int, array{student: User, attempts_count: int, average_percent: float, last_activity: Carbon|null, attempts: Collection<int, TestAttempt>}>
     */
    public function studentsForTeacher(int $teacherId): Collection
    {
        $topicTestIds = TopicTest::where('user_id', $teacherId)->pluck('id');
        $dtmTestIds = DtmTest::where('user_id', $teacherId)->pluck('id');
        $sertifikatTestIds = SertifikatTest::where('user_id', $teacherId)->pluck('id');
        $languageExamTestIds = LanguageExamTest::where('user_id', $teacherId)->pluck('id');

        $attempts = TestAttempt::with(['user', 'testable'])
            ->where('status', 'submitted')
            ->where(function ($query) use ($topicTestIds, $dtmTestIds, $sertifikatTestIds, $languageExamTestIds) {
                $query->where(fn ($q) => $q->where('testable_type', TopicTest::class)->whereIn('testable_id', $topicTestIds))
                    ->orWhere(fn ($q) => $q->where('testable_type', DtmTest::class)->whereIn('testable_id', $dtmTestIds))
                    ->orWhere(fn ($q) => $q->where('testable_type', SertifikatTest::class)->whereIn('testable_id', $sertifikatTestIds))
                    ->orWhere(fn ($q) => $q->where('testable_type', LanguageExamTest::class)->whereIn('testable_id', $languageExamTestIds));
            })
            ->latest('submitted_at')
            ->get();

        return $attempts->groupBy('user_id')
            ->map(function (Collection $studentAttempts) {
                $graded = $studentAttempts->filter(fn (TestAttempt $a) => ! $a->hasPendingGrading());

                return [
                    'student' => $studentAttempts->first()->user,
                    'attempts_count' => $studentAttempts->count(),
                    'average_percent' => $graded->isNotEmpty()
                        ? round($graded->avg(fn (TestAttempt $a) => $a->max_score > 0 ? ($a->score / $a->max_score * 100) : 0))
                        : 0,
                    'last_activity' => $studentAttempts->max('submitted_at'),
                    'attempts' => $studentAttempts->values(),
                ];
            })
            ->sortByDesc('last_activity')
            ->values();
    }

    /**
     * A single attempt's full question-by-question detail, for the teacher
     * who owns the test — mirrors StudentTestService::result() but authorizes
     * against the test's owner instead of the attempt's owner.
     */
    public function resultForTeacher(TestAttempt $attempt, int $teacherId): TestAttempt
    {
        $attempt->loadMissing('testable');

        if (! $attempt->testable || $attempt->testable->user_id !== $teacherId) {
            throw new Exception('Bu urinishga kira olmaysiz', 403);
        }

        return $attempt->load(['answers.questionable', 'testable', 'user']);
    }
}
