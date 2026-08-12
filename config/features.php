<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Video darslar (lessons)
    |--------------------------------------------------------------------------
    |
    | To'liq ishga tushirilgan. Kerak bo'lsa .env'da
    | FEATURE_LESSONS_ENABLED=false qilib vaqtincha yopib qo'yish ham mumkin —
    | kodning o'zi shu bayroq orqali butunlay saqlanib qoladi.
    |
    */
    'lessons_enabled' => env('FEATURE_LESSONS_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Guruhlar va jonli darslar (live lessons)
    |--------------------------------------------------------------------------
    |
    | LiveKit orqali ishlaydigan jonli video-dars bo'limi. LIVEKIT_API_KEY /
    | LIVEKIT_API_SECRET / LIVEKIT_URL to'ldirilmagan bo'lsa ham bo'lim
    | ko'rinadi, lekin xonaga kirishda tushunarli xatolik ko'rsatiladi —
    | shuning uchun standart holatda yoqilgan.
    |
    */
    'live_lessons_enabled' => env('FEATURE_LIVE_LESSONS_ENABLED', true),

];
