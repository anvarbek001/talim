<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Asosiy SEO sozlamalari
    |--------------------------------------------------------------------------
    |
    | Bu qiymatlar sayt bo'ylab meta teglar, Open Graph, Twitter Card va
    | JSON-LD structured data uchun ishlatiladi. Production serverda
    | .env faylida APP_URL=https://darsqil.uz qilib qo'yilishi shart —
    | canonical, sitemap va og:url shu qiymatga tayanadi.
    |
    */

    'url' => rtrim(config('app.url'), '/'),

    'site_name' => env('APP_NAME', 'DarsQil'),

    'default_title' => "DarsQil — Video darslar orqali onlayn ta'lim platformasi",

    'default_description' => "DarsQil — matematika, fizika, dasturlash va boshqa fanlar bo'yicha "
        ."tajribali o'qituvchilarning video darslarini tomosha qiling. Onlayn ta'lim, DTM va "
        ."sertifikat testlariga tayyorgarlik bir joyda.",

    'default_keywords' => "video darslar, onlayn ta'lim, DarsQil, matematika darslari, fizika darslari, "
        ."DTM tayyorgarlik, sertifikat testlari, onlayn kurslar, masofaviy ta'lim, o'zbek tilida darslar",

    // 1200x630 o'lchamdagi rasmni public/images/og-cover.jpg ga joylashtiring.
    'og_image' => '/images/og-cover.jpg',

    // Google Search Console -> Settings -> Ownership verification -> HTML tag dan olinadi.
    'google_verification' => env('GOOGLE_SITE_VERIFICATION', ''),

    // Yandex Webmaster -> Sozlamalar -> Huquqlarni tasdiqlash -> meta teg dan olinadi.
    'yandex_verification' => env('YANDEX_SITE_VERIFICATION', ''),

];
