<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class TopicTest extends Model
{
    protected $fillable = [
        'user_id',
        'science_id',
        'grade_id',
        'section_id',
        'topic_id',
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

    public function grade(): BelongsTo
    {
        return $this->belongsTo(Grade::class);
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(TopicTestQuestion::class)->orderBy('order');
    }

    public function attempts(): MorphMany
    {
        return $this->morphMany(TestAttempt::class, 'testable');
    }
}
