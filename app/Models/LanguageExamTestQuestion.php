<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LanguageExamTestQuestion extends Model
{
    protected $fillable = [
        'language_exam_test_id',
        'question',
        'order',
    ];

    public function languageExamTest(): BelongsTo
    {
        return $this->belongsTo(LanguageExamTest::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(LanguageExamTestOption::class)->orderBy('order');
    }
}
