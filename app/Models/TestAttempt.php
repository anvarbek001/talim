<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class TestAttempt extends Model
{
    protected $fillable = [
        'user_id',
        'testable_type',
        'testable_id',
        'status',
        'started_at',
        'submitted_at',
        'score',
        'max_score',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'submitted_at' => 'datetime',
            'score' => 'float',
            'max_score' => 'float',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function testable(): MorphTo
    {
        return $this->morphTo();
    }

    public function answers(): HasMany
    {
        return $this->hasMany(TestAttemptAnswer::class);
    }

    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    public function isSubmitted(): bool
    {
        return $this->status === 'submitted';
    }

    public function hasPendingGrading(): bool
    {
        return $this->answers()->whereNull('graded_at')->exists();
    }
}
