<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
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

    'deeplink' => [
        'secret' => env('DEEPLINK_HMAC_SECRET'),
        'base_url' => env('DEEPLINK_BASE_URL', 'https://desert.rxstudio.dev'),
        'play_store_url' => env('DEEPLINK_PLAY_STORE_URL', 'https://play.google.com/store/apps/details?id=ar.com.deserteventos.app'),
        'app_store_url' => env('DEEPLINK_APP_STORE_URL', 'https://apps.apple.com/app/id000000000'),
        'feature' => 'invite',
        'default_ttl_days' => 30,
    ],

];
