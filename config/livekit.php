<?php

return [

    /*
    |--------------------------------------------------------------------------
    | LiveKit sozlamalari
    |--------------------------------------------------------------------------
    |
    | https://cloud.livekit.io dan bepul loyiha yarating (yoki o'z serveringizga
    | LiveKit'ni joylashtiring). "Settings -> Keys" bo'limidan API Key, API
    | Secret va WebSocket URL (wss://...) ni oling, .env fayliga qo'ying:
    |
    |   LIVEKIT_URL=wss://your-project.livekit.cloud
    |   LIVEKIT_API_KEY=APIxxxxxxxx
    |   LIVEKIT_API_SECRET=xxxxxxxxxxxxxxxxxxxxxxxxxxxx
    |
    */

    'url' => env('LIVEKIT_URL', ''),
    'api_key' => env('LIVEKIT_API_KEY', ''),
    'api_secret' => env('LIVEKIT_API_SECRET', ''),

    // Kirish tokeni necha soniyadan keyin eskiradi (standart: 4 soat).
    'token_ttl' => env('LIVEKIT_TOKEN_TTL', 4 * 60 * 60),

];
