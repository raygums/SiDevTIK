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

    /*
    |--------------------------------------------------------------------------
    | SSO Unila Configuration
    |--------------------------------------------------------------------------
    |
    | Konfigurasi untuk integrasi SSO akses.unila.ac.id
    |
    */
    'sso' => [
        'app_key' => env('SSO_APP_KEY'),
        'jwt_secret' => env('SSO_JWT_SECRET', 'secret'),
        'base_url' => env('SSO_BASE_URL', 'https://akses.unila.ac.id/api/live/v1/auth'),
        'logout_url' => env('SSO_LOGOUT_URL', 'https://akses.unila.ac.id/auth/logout'),
    ],

];
