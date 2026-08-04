<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LessonFile extends Model
{
    protected $fillable = [
        'lesson_id',
        'type',  // 'file' yoki 'youtube'
        'lesson_file',  // diskdagi yo'l (type = file bo'lsa, yoki youtube uchun vaqtinchalik pending fayl yo'li)
        'youtube_id',  // YouTube video ID (type = youtube bo'lsa)
        'status',  // 'pending' | 'ready' | 'failed' — youtube video fon jarayonida yuklanayotganini bildiradi
    ];

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    public function isVideo(): bool
    {
        return $this->type === 'youtube';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function embedUrl(): ?string
    {
        return $this->youtube_id
            ? "https://www.youtube.com/embed/{$this->youtube_id}"
            : null;
    }
}
