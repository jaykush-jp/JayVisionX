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

    // Anthropic (Claude) — one option for the AI support chatbot.
    // Leave the key empty to run the chatbot in free DB/FAQ fallback mode.
    'anthropic' => [
        'key'   => env('ANTHROPIC_API_KEY'),
        'model' => env('ANTHROPIC_MODEL', 'claude-haiku-5-5'),
    ],

    // Google Gemini — alternative AI provider for the chatbot. Set the AI
    // Configuration provider to "gemini" (or leave only this key set) to use it.
    'gemini' => [
        'key'   => env('GEMINI_API_KEY'),
        'model' => env('GEMINI_MODEL', 'gemini-2.0-flash'),
    ],

    // Google Sheets — leads are POSTed to this Apps Script web-app URL.
    'google_sheets' => [
        'webhook_url' => env('GOOGLE_SHEETS_WEBHOOK_URL'),
    ],

    // Where new-lead email notifications are sent (falls back to the site email setting).
    'leads' => [
        'notify_email' => env('LEAD_NOTIFY_EMAIL'),
    ],

    // Google OAuth — powers the Google Business Profile integration
    // (reviews + Google Posts). Leave empty to keep the integration in its
    // "not configured" state. Requires an approved Business Profile API app.
    'google' => [
        'client_id'     => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'scopes'        => env('GOOGLE_SCOPES', 'https://www.googleapis.com/auth/business.manage'),
        // Shared secret you set on the Google Ads lead-form webhook.
        'lead_webhook_key' => env('GOOGLE_LEAD_WEBHOOK_KEY'),
    ],

    // Meta (Facebook + Instagram) app — powers social publishing. Requires
    // Business Verification + App Review for the publishing permissions.
    'meta' => [
        'app_id'        => env('META_APP_ID'),
        'app_secret'    => env('META_APP_SECRET'),
        'graph_version' => env('META_GRAPH_VERSION', 'v21.0'),
        'scopes'        => env('META_SCOPES', 'pages_manage_posts,pages_read_engagement,pages_show_list,leads_retrieval,instagram_basic,instagram_content_publish'),
        // Lead Ads webhook: the verify token you set in the Meta app, and a
        // Page access token with leads_retrieval (falls back to the OAuth token).
        'lead_verify_token'  => env('META_LEAD_VERIFY_TOKEN'),
        'page_access_token'  => env('META_PAGE_ACCESS_TOKEN'),
    ],

];
