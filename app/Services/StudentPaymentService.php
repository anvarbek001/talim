<?php

namespace App\Services;

use App\Models\Book;
use App\Models\DtmTest;
use App\Models\GroupPlan;
use App\Models\LanguageExamTest;
use App\Models\Purchase;
use App\Models\Section;
use App\Models\SertifikatTest;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class StudentPaymentService
{
    /**
     * Purchasable types shown on the student's payment history, keyed by
     * their FQCN and labeled with the Uzbek name shown in the UI.
     *
     * @var array<class-string, string>
     */
    public const TYPE_LABELS = [
        Book::class => 'Kitob',
        Section::class => "Bo'lim",
        DtmTest::class => 'DTM testi',
        SertifikatTest::class => 'Sertifikat testi',
        LanguageExamTest::class => 'Til imtihoni',
        User::class => "O'qituvchi obunasi",
        GroupPlan::class => 'Guruh tarifi',
    ];

    public function history(int $userId): LengthAwarePaginator
    {
        return Purchase::where('user_id', $userId)
            ->with('purchasable')
            ->latest()
            ->paginate(15)
            ->withQueryString();
    }

    public function totalSpent(int $userId): float
    {
        return (float) Purchase::where('user_id', $userId)->sum('price');
    }

    public function purchasesCount(int $userId): int
    {
        return Purchase::where('user_id', $userId)->count();
    }
}
