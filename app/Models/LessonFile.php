<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class LessonFile extends Model
{
    protected $fillable = [
        'lesson_id',
        'type',  // 'file' yoki 'youtube'
        'lesson_file',  // diskdagi yo'l (type = file bo'lsa)
        'youtube_id',  // YouTube video ID (type = youtube bo'lsa)
    ];

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    public function isVideo(): bool
    {
        return $this->type === 'youtube';
    }

    public function embedUrl(): ?string
    {
        return $this->youtube_id
            ? "https://www.youtube-nocookie.com/embed/{$this->youtube_id}"
            : null;
    }
}
