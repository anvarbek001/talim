<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class SertifikatTest extends Model
{
    protected $fillable = [
        'user_id',
        'science_id',
        'level',
        'title',
        'description',
        'duration_minutes',
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
        return $this->hasMany(SertifikatTestQuestion::class)->orderBy('order');
    }

    public function writtenQuestions(): HasMany
    {
        return $this->hasMany(SertifikatTestWrittenQuestion::class)->orderBy('order');
    }

    public function attempts(): MorphMany
    {
        return $this->morphMany(TestAttempt::class, 'testable');
    }
}
