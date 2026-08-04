<?php

namespace App\Services;

use App\Models\DtmTest;
use App\Models\Lesson;
use App\Models\SertifikatTest;
use App\Models\TestAttempt;
use App\Models\TopicTest;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Role;

class StudentStatisticsService
{
    /** @var array<string, class-string> */
    protected const TYPE_LABELS = [
        TopicTest::class => 'Mavzu testi',
        DtmTest::class => 'DTM testi',
        SertifikatTest::class => 'Sertifikat testi',
    ];

    /**
     * @return array<string, mixed>
     */
    public function forUser(int $userId): array
    {
        $submitted = TestAttempt::where('user_id', $userId)
            ->where('status', 'submitted')
            ->get();

        $graded = $submitted->filter(fn (TestAttempt $a) => ! $a->hasPendingGrading());

        $averagePercent = $graded->isNotEmpty()
            ? round($graded->avg(fn (TestAttempt $a) => $a->max_score > 0 ? ($a->score / $a->max_score * 100) : 0))
            : 0;

        $byType = $submitted->groupBy('testable_type')->map(function (Collection $attempts, string $type) {
            $gradedOfType = $attempts->filter(fn (TestAttempt $a) => ! $a->hasPendingGrading());

            return [
                'label' => self::TYPE_LABELS[$type] ?? $type,
                'count' => $attempts->count(),
                'average_percent' => $gradedOfType->isNotEmpty()
                    ? round($gradedOfType->avg(fn (TestAttempt $a) => $a->max_score > 0 ? ($a->score / $a->max_score * 100) : 0))
                    : 0,
            ];
        })->values();

        $recentAttempts = TestAttempt::where('user_id', $userId)
            ->where('status', 'submitted')
            ->with('testable')
            ->latest('submitted_at')
            ->limit(8)
            ->get();

        return [
            'total_lessons' => Lesson::count(),
            'saved_lessons_count' => User::findOrFail($userId)->savedLessons()->count(),
            'attempts_submitted' => $submitted->count(),
            'attempts_in_progress' => TestAttempt::where('user_id', $userId)->where('status', 'in_progress')->count(),
            'average_percent' => $averagePercent,
            'by_type' => $byType,
            'recent_attempts' => $recentAttempts,
        ];
    }

    /**
     * Ranks students by their total score across submitted attempts. Cached
     * briefly since every student's dashboard visit triggers this query.
     *
     * @return Collection<int, array{user: User, total_score: float, attempts_count: int}>
     */
    public function leaderboard(int $limit = 10): Collection
    {
        if (! Role::where('name', 'student')->where('guard_name', 'web')->exists()) {
            return collect();
        }

        return Cache::remember("student.leaderboard.{$limit}", 120, function () use ($limit) {
            return User::role('student')
                ->withSum(['testAttempts as total_score' => fn ($query) => $query->where('status', 'submitted')], 'score')
                ->withCount(['testAttempts as attempts_count' => fn ($query) => $query->where('status', 'submitted')])
                ->orderByDesc('total_score')
                ->limit($limit)
                ->get()
                ->map(fn (User $user) => [
                    'user' => $user,
                    'total_score' => (float) ($user->total_score ?? 0),
                    'attempts_count' => (int) $user->attempts_count,
                ]);
        });
    }
}
