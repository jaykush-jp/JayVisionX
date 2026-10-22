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
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
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

    // Anthropic (Claude) — powers the AI support chatbot.
    // Leave the key empty to run the chatbot in free DB/FAQ fallback mode.
    'anthropic' => [
        'key'   => env('ANTHROPIC_API_KEY'),
        'model' => env('ANTHROPIC_MODEL', 'claude-haiku-5-5'),
    ],

    // Google Sheets — leads are POSTed to this Apps Script web-app URL.
    'google_sheets' => [
        'webhook_url' => env('GOOGLE_SHEETS_WEBHOOK_URL'),
    ],

    // Where new-lead email notifications are sent (falls back to the site email setting).
    'leads' => [
        'notify_email' => env('LEAD_NOTIFY_EMAIL'),
    ],

];
