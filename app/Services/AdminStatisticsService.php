<?php

namespace App\Services;

use App\Models\Book;
use App\Models\DtmTest;
use App\Models\Lesson;
use App\Models\Purchase;
use App\Models\SertifikatTest;
use App\Models\TopicTest;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class AdminStatisticsService
{
    public const PLATFORM_COMMISSION_RATE = 0.20;

    /**
     * Dashboard-wide counters, cached briefly — this aggregate runs 7 counts
     * across the whole platform, so a short TTL trades a little staleness
     * for far fewer queries on repeated dashboard visits.
     *
     * @return array<string, mixed>
     */
    public function overview(): array
    {
        return Cache::remember('admin.stats.overview', 120, function () {
            $grossRevenue = (float) Purchase::sum('price');

            return [
                'teachers_count' => User::role('teacher')->count(),
                'students_count' => User::role('student')->count(),
                'lessons_count' => Lesson::count(),
                'tests_count' => TopicTest::count() + DtmTest::count() + SertifikatTest::count(),
                'books_count' => Book::count(),
                'purchases_count' => Purchase::count(),
                'gross_revenue' => $grossRevenue,
                'platform_profit' => round($grossRevenue * self::PLATFORM_COMMISSION_RATE),
                'teacher_payouts' => round($grossRevenue * (1 - self::PLATFORM_COMMISSION_RATE)),
            ];
        });
    }

    /**
     * @return Collection<int, User>
     */
    public function topByBooks(int $limit = 5): Collection
    {
        return User::role('teacher')
            ->withCount('books')
            ->orderByDesc('books_count')
            ->get()
            ->filter(fn (User $teacher) => $teacher->books_count > 0)
            ->take($limit)
            ->values();
    }

    /**
     * @return Collection<int, User>
     */
    public function topByLessons(int $limit = 5): Collection
    {
        return User::role('teacher')
            ->withCount('lessons')
            ->orderByDesc('lessons_count')
            ->get()
            ->filter(fn (User $teacher) => $teacher->lessons_count > 0)
            ->take($limit)
            ->values();
    }

    /**
     * @return Collection<int, array{teacher: User, tests_count: int}>
     */
    public function topByTests(int $limit = 5): Collection
    {
        return User::role('teacher')
            ->withCount(['topicTests', 'dtmTests', 'sertifikatTests'])
            ->get()
            ->map(fn (User $teacher) => [
                'teacher' => $teacher,
                'tests_count' => $teacher->topic_tests_count + $teacher->dtm_tests_count + $teacher->sertifikat_tests_count,
            ])
            ->filter(fn (array $row) => $row['tests_count'] > 0)
            ->sortByDesc('tests_count')
            ->take($limit)
            ->values();
    }

    /**
     * Ranks teachers by revenue earned across all their purchasable content
     * (sections, DTM/sertifikat tests, books). A purchase's owning teacher
     * is only reachable through its polymorphic `purchasable->user`, so
     * purchases are eager-loaded and grouped in PHP rather than joined in
     * SQL across four differently-shaped tables.
     *
     * @return Collection<int, array{teacher: User, lessons_count: int, tests_count: int, books_count: int, purchases_count: int, gross: float, earning: float, platform_cut: float}>
     */
    public function teacherRevenue(): Collection
    {
        $purchasesByTeacher = Purchase::with('purchasable.user')
            ->get()
            ->filter(fn (Purchase $purchase) => $purchase->purchasable && $purchase->purchasable->user)
            ->groupBy(fn (Purchase $purchase) => $purchase->purchasable->user_id);

        // Batch-load every teacher's content counts up front instead of
        // running 5 count queries per teacher inside the map() below.
        $teacherStats = User::whereIn('id', $purchasesByTeacher->keys())
            ->withCount(['lessons', 'topicTests', 'dtmTests', 'sertifikatTests', 'books'])
            ->get()
            ->keyBy('id');

        return $purchasesByTeacher
            ->map(function (Collection $purchases) use ($teacherStats) {
                $teacher = $purchases->first()->purchasable->user;
                $stats = $teacherStats->get($teacher->id);
                $gross = (float) $purchases->sum('price');

                return [
                    'teacher' => $teacher,
                    'lessons_count' => $stats->lessons_count,
                    'tests_count' => $stats->topic_tests_count + $stats->dtm_tests_count + $stats->sertifikat_tests_count,
                    'books_count' => $stats->books_count,
                    'purchases_count' => $purchases->count(),
                    'gross' => $gross,
                    'earning' => round($gross * (1 - self::PLATFORM_COMMISSION_RATE)),
                    'platform_cut' => round($gross * self::PLATFORM_COMMISSION_RATE),
                ];
            })
            ->sortByDesc('gross')
            ->values();
    }
}
