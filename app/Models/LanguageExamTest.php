<?php

namespace App\Models;

use App\Contracts\Purchasable;
use App\Models\Concerns\IsPurchasable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class LanguageExamTest extends Model implements Purchasable
{
    use IsPurchasable;

    /**
     * O'zbekistonda talab qilinadigan asosiy xalqaro va milliy til
     * sertifikatlash tizimlari — teacher shundan birini tanlaydi.
     *
     * @var array<string, string>
     */
    public const EXAM_TYPES = [
        'IELTS' => 'IELTS',
        'TOEFL' => 'TOEFL',
        'CEFR' => 'CEFR',
        'MULTILEVEL' => 'Multilevel (Milliy sertifikat)',
        'CAMBRIDGE' => 'Cambridge (FCE/CAE/CPE)',
        'DELF_DALF' => 'DELF/DALF',
        'TESTDAF' => 'TestDaF/Goethe-Zertifikat',
        'TOPIK' => 'TOPIK',
        'HSK' => 'HSK',
        'TORFL' => 'TORFL',
    ];

    protected $fillable = [
        'user_id',
        'science_id',
        'exam_type',
        'level',
        'title',
        'description',
        'duration_minutes',
        'price',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function science(): BelongsTo
    {
        return $this->belongsTo(Science::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(LanguageExamTestQuestion::class)->orderBy('order');
    }

    public function writtenQuestions(): HasMany
    {
        return $this->hasMany(LanguageExamTestWrittenQuestion::class)->orderBy('order');
    }

    public function attempts(): MorphMany
    {
        return $this->morphMany(TestAttempt::class, 'testable');
    }

    public function examTypeLabel(): string
    {
        return self::EXAM_TYPES[$this->exam_type] ?? $this->exam_type;
    }
}
