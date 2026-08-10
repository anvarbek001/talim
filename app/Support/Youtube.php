<?php

namespace App\Support;

class Youtube
{
    /**
     * YouTube havolasidan video ID'sini ajratib oladi. Turli formatlarni
     * qo'llab-quvvatlaydi: youtu.be/ID, youtube.com/watch?v=ID,
     * youtube.com/embed/ID, youtube.com/shorts/ID, youtube.com/v/ID.
     * Havola noto'g'ri yoki YouTube havolasi bo'lmasa — null qaytaradi.
     */
    public static function extractVideoId(string $url): ?string
    {
        $patterns = [
            '~youtu\.be/([A-Za-z0-9_-]{11})~i',
            '~youtube(?:-nocookie)?\.com/.*[?&]v=([A-Za-z0-9_-]{11})~i',
            '~youtube(?:-nocookie)?\.com/embed/([A-Za-z0-9_-]{11})~i',
            '~youtube(?:-nocookie)?\.com/shorts/([A-Za-z0-9_-]{11})~i',
            '~youtube(?:-nocookie)?\.com/v/([A-Za-z0-9_-]{11})~i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $url, $matches)) {
                return $matches[1];
            }
        }

        return null;
    }
}
