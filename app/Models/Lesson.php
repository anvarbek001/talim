<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lesson extends Model
{
    /**
     * How many of the earliest lessons per science stay watchable without
     * an active subscription.
     */
    public const FREE_PREVIEW_COUNT = 3;

    protected $fillable = [
        'user_id',
        'science_id',
        'grade_id',
        'section_id',
        'topic_id',
        'title',
        'description',
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

    public function lessonfiles(): HasMany
    {
        return $this->hasMany(LessonFile::class);
    }

    public function savedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'saved_lessons')->withTimestamps();
    }

    /**
     * True for the earliest lessons of each science — always watchable,
     * even without an active subscription.
     */
    public function isFreePreview(): bool
    {
        $earlierCount = static::where('science_id', $this->science_id)
            ->where(function ($query) {
                $query->where('created_at', '<', $this->created_at)
                    ->orWhere(function ($query) {
                        $query->where('created_at', $this->created_at)->where('id', '<', $this->id);
                    });
            })
            ->count();

        return $earlierCount < self::FREE_PREVIEW_COUNT;
    }
}
