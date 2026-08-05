<?php

return [
    /*
     * |--------------------------------------------------------------------------
     * | Third Party Services
     * |--------------------------------------------------------------------------
     * |
     * | This file is for storing the credentials for third party services such
     * | as Mailgun, Postmark, AWS and more. This file provides the de facto
     * | location for this type of information, allowing packages to have
     * | a conventional file to locate the various service credentials.
     * |
     */
    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],
    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],
    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],
    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],
    'youtube' => [
        'client_id' => env('YOUTUBE_CLIENT_ID'),
        'client_secret' => env('YOUTUBE_CLIENT_SECRET'),
        'redirect_uri' => env('YOUTUBE_REDIRECT_URI'),
        'refresh_token' => env('YOUTUBE_REFRESH_TOKEN'),
    ],

    // Click.uz Merchant Shop-API — https://docs.click.uz/en/click-request/
    // Merchant kabinetdan (https://merchant.click.uz) olinadi.
    'click' => [
        'merchant_id' => env('CLICK_MERCHANT_ID'),
        'service_id' => env('CLICK_SERVICE_ID'),
        'merchant_user_id' => env('CLICK_MERCHANT_USER_ID'),
        'secret_key' => env('CLICK_SECRET_KEY'),
        'checkout_url' => env('CLICK_CHECKOUT_URL', 'https://my.click.uz/services/pay'),
        'return_url' => env('CLICK_RETURN_URL'),
    ],
];
