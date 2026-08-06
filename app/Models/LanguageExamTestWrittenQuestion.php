<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LanguageExamTestWrittenQuestion extends Model
{
    protected $fillable = [
        'language_exam_test_id',
        'question',
        'max_score',
        'order',
    ];

    public function languageExamTest(): BelongsTo
    {
        return $this->belongsTo(LanguageExamTest::class);
    }
}
