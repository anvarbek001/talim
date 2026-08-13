<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LessonFile extends Model
{
    protected $fillable = [
        'lesson_id',
        'title',  // video nomi (type = youtube bo'lsa) — bitta darsda bir nechta video bo'lishi mumkin
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

    /**
     * youtube-nocookie.com (maxfiylikni kuchaytirilgan rejimi) + kamroq
     * YouTube brendlash/tavsiya havolalari — video ID baribir iframe src'da
     * ko'rinadi (bu ko'rsatishning tabiiy chegarasi), lekin bu havola endi
     * hech qachon sahifa HTML'ida oldindan tayyor turmaydi — faqat
     * LessonFileController::embed() orqali, ruxsat tekshirilgach beriladi
     * (qarang: routes/web.php'dagi lesson-files.embed).
     */
    public function embedUrl(): ?string
    {
        return $this->youtube_id
            ? "https://www.youtube-nocookie.com/embed/{$this->youtube_id}?modestbranding=1&rel=0"
            : null;
    }
}
