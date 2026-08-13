<?php

namespace App\Services;

use App\Models\Book;
use App\Models\DtmTest;
use App\Models\LanguageExamTest;
use App\Models\Lesson;
use App\Models\Purchase;
use App\Models\Section;
use App\Models\SertifikatTest;
use App\Models\TestAttempt;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * O'qituvchi bosh sahifasi ("teacher_dashboard") uchun — barcha ko'rsatkichlar
 * real bazadagi yozuvlardan hisoblanadi (avvalgi versiyada bularning hammasi
 * qattiq yozilgan soxta raqamlar edi). Daromad hisobi AdminStatisticsService
 * bilan bir xil komissiya ulushidan foydalanadi.
 */
class TeacherStatisticsService
{
    /** @var array<int, string> */
    protected const UZ_WEEKDAYS = [
        1 => 'Dush', 2 => 'Sesh', 3 => 'Chor', 4 => 'Pay', 5 => 'Jum', 6 => 'Shan', 7 => 'Yak',
    ];

    public function __construct(protected TeacherStudentService $teacherStudentServ) {}

    /**
     * @return array<string, mixed>
     */
    public function overview(int $teacherId): array
    {
        $monthStart = now()->startOfMonth();
        $prevMonthStart = now()->subMonthNoOverflow()->startOfMonth();
        $prevMonthEnd = now()->startOfMonth();

        $lessonsCount = Lesson::where('user_id', $teacherId)->count();
        $lessonsDelta = Lesson::where('user_id', $teacherId)->where('created_at', '>=', $monthStart)->count();

        $subscriberPurchases = $this->subscriberPurchasesQuery($teacherId)->get(['created_at', 'expires_at']);
        $subscribersCount = $subscriberPurchases->filter(fn (Purchase $p) => $p->isActive())->count();
        $subscribersDelta = $subscriberPurchases->where('created_at', '>=', $monthStart)->count();

        $studentsCount = $this->teacherStudentServ->studentsForTeacher($teacherId)->count();

        $thisMonthRevenue = $this->netRevenue($teacherId, $monthStart, now());
        $prevMonthRevenue = $this->netRevenue($teacherId, $prevMonthStart, $prevMonthEnd);
        $revenueDeltaPercent = $prevMonthRevenue > 0
            ? round((($thisMonthRevenue - $prevMonthRevenue) / $prevMonthRevenue) * 100, 1)
            : ($thisMonthRevenue > 0 ? 100.0 : 0.0);

        return [
            'lessons_count' => $lessonsCount,
            'lessons_delta' => $lessonsDelta,
            'subscribers_count' => $subscribersCount,
            'subscribers_delta' => $subscribersDelta,
            'students_count' => $studentsCount,
            'monthly_revenue' => $thisMonthRevenue,
            'revenue_delta_percent' => $revenueDeltaPercent,
        ];
    }

    /**
     * So'nggi 7 kun (bugungi kun bilan) — har biriga shu o'qituvchining
     * testlariga topshirilgan urinishlar soni. "Ko'rishlar" o'rniga real
     * o'quvchi faolligini ko'rsatadi.
     *
     * @return Collection<int, array{label: string, date: string, count: int}>
     */
    public function weeklyActivity(int $teacherId): Collection
    {
        $since = now()->subDays(6)->startOfDay();

        $attempts = $this->teacherStudentServ->studentsForTeacher($teacherId)
            ->pluck('attempts')
            ->flatten(1)
            ->filter(fn (TestAttempt $attempt) => $attempt->submitted_at && $attempt->submitted_at->gte($since));

        $byDay = $attempts->groupBy(fn (TestAttempt $attempt) => $attempt->submitted_at->format('Y-m-d'));

        return collect(range(6, 0))
            ->map(function (int $daysAgo) use ($byDay) {
                $date = now()->subDays($daysAgo);

                return [
                    'label' => self::UZ_WEEKDAYS[$date->dayOfWeekIso],
                    'date' => $date->format('Y-m-d'),
                    'count' => $byDay->get($date->format('Y-m-d'), collect())->count(),
                ];
            })
            ->values();
    }

    /**
     * @return Collection<int, Lesson>
     */
    public function recentLessons(int $teacherId, int $limit = 3): Collection
    {
        return Lesson::where('user_id', $teacherId)
            ->with('science')
            ->withCount('savedByUsers')
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * O'qituvchining kontenti (bo'lim/kitob/test) yoki o'ziga obunasi
     * bo'yicha so'nggi xaridlar — "sharhlar" o'rniga real savdo oqimi.
     *
     * @return Collection<int, Purchase>
     */
    public function recentPurchases(int $teacherId, int $limit = 5): Collection
    {
        return $this->teacherPurchasesQuery($teacherId)
            ->with(['user', 'purchasable'])
            ->latest()
            ->limit($limit)
            ->get();
    }

    protected function netRevenue(int $teacherId, $from, $to): int
    {
        $gross = (float) $this->teacherPurchasesQuery($teacherId)
            ->whereBetween('created_at', [$from, $to])
            ->sum('price');

        return (int) round($gross * (1 - AdminStatisticsService::PLATFORM_COMMISSION_RATE));
    }

    /**
     * Shu o'qituvchining sotiladigan kontenti (bo'lim/kitob/DTM/sertifikat/til
     * imtihoni testi) yoki o'zining obunasi bo'yicha barcha xaridlar.
     */
    protected function teacherPurchasesQuery(int $teacherId)
    {
        $sectionIds = Section::where('user_id', $teacherId)->pluck('id');
        $bookIds = Book::where('user_id', $teacherId)->pluck('id');
        $dtmIds = DtmTest::where('user_id', $teacherId)->pluck('id');
        $sertifikatIds = SertifikatTest::where('user_id', $teacherId)->pluck('id');
        $languageExamIds = LanguageExamTest::where('user_id', $teacherId)->pluck('id');

        return Purchase::where(function ($query) use ($teacherId, $sectionIds, $bookIds, $dtmIds, $sertifikatIds, $languageExamIds) {
            $query->where(fn ($q) => $q->where('purchasable_type', Section::class)->whereIn('purchasable_id', $sectionIds))
                ->orWhere(fn ($q) => $q->where('purchasable_type', Book::class)->whereIn('purchasable_id', $bookIds))
                ->orWhere(fn ($q) => $q->where('purchasable_type', DtmTest::class)->whereIn('purchasable_id', $dtmIds))
                ->orWhere(fn ($q) => $q->where('purchasable_type', SertifikatTest::class)->whereIn('purchasable_id', $sertifikatIds))
                ->orWhere(fn ($q) => $q->where('purchasable_type', LanguageExamTest::class)->whereIn('purchasable_id', $languageExamIds))
                ->orWhere(fn ($q) => $q->where('purchasable_type', User::class)->where('purchasable_id', $teacherId));
        });
    }

    /**
     * Faqat o'qituvchining o'ziga (User::class) qilingan obuna xaridlari.
     */
    protected function subscriberPurchasesQuery(int $teacherId)
    {
        return Purchase::where('purchasable_type', User::class)->where('purchasable_id', $teacherId);
    }
}
