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

    'stripe' => [
        'starter_monthly_price_id' => env('STRIPE_STARTER_MONTHLY_PRICE_ID'),
        'starter_annual_price_id' => env('STRIPE_STARTER_ANNUAL_PRICE_ID'),
        'professional_monthly_price_id' => env('STRIPE_PROFESSIONAL_MONTHLY_PRICE_ID'),
        'professional_annual_price_id' => env('STRIPE_PROFESSIONAL_ANNUAL_PRICE_ID'),
        'enterprise_monthly_price_id' => env('STRIPE_ENTERPRISE_MONTHLY_PRICE_ID'),
        'enterprise_annual_price_id' => env('STRIPE_ENTERPRISE_ANNUAL_PRICE_ID'),
    ],

];
