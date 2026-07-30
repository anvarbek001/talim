<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class TestAttemptAnswer extends Model
{
    protected $fillable = [
        'test_attempt_id',
        'questionable_type',
        'questionable_id',
        'selected_option_id',
        'answer_text',
        'score',
        'max_score',
        'graded_at',
    ];

    protected function casts(): array
    {
        return [
            'graded_at' => 'datetime',
            'score' => 'float',
            'max_score' => 'float',
        ];
    }

    public function attempt(): BelongsTo
    {
        return $this->belongsTo(TestAttempt::class, 'test_attempt_id');
    }

    public function questionable(): MorphTo
    {
        return $this->morphTo()->morphWith([
            TopicTestQuestion::class => ['options'],
            DtmTestQuestion::class => ['options'],
            SertifikatTestQuestion::class => ['options'],
        ]);
    }

    public function isGraded(): bool
    {
        return $this->graded_at !== null;
    }
}
