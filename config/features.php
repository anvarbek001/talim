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

];
