<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Video darslar (lessons)
    |--------------------------------------------------------------------------
    |
    | Vaqtincha o'chirilgan — hozircha faqat testlar va kitoblar sotiladi.
    | Kodning o'zi butunlay saqlanib qolgan; buni true qilib qo'yish
    | (yoki .env'da FEATURE_LESSONS_ENABLED=true) bo'lim va navigatsiyani
    | darhol qayta yoqadi.
    |
    */
    'lessons_enabled' => env('FEATURE_LESSONS_ENABLED', false),

];
