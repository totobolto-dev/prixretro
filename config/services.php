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

    'google_analytics' => [
        'id' => env('GOOGLE_ANALYTICS_ID', 'G-4QPNVF0BRW'),
    ],

    'ebay' => [
        'app_id' => env('EBAY_SANDBOX_APP_ID') ?: env('EBAY_APP_ID'),
        'dev_id' => env('EBAY_SANDBOX_DEV_ID') ?: env('EBAY_DEV_ID'),
        'cert_id' => env('EBAY_SANDBOX_CERT_ID') ?: env('EBAY_CERT_ID'),
        'verification_token' => env('EBAY_VERIFICATION_TOKEN'),

        // API endpoints (use Sandbox if credentials available, else Production)
        'finding_api_url' => env('EBAY_SANDBOX_APP_ID')
            ? 'https://svcs.sandbox.ebay.com/services/search/FindingService/v1'
            : 'https://svcs.ebay.com/services/search/FindingService/v1',
        'shopping_api_url' => env('EBAY_SANDBOX_APP_ID')
            ? 'http://open.api.sandbox.ebay.com/shopping'
            : 'http://open.api.ebay.com/shopping',

        // Site ID for eBay France
        'site_id' => 71, // EBAY-FR

        // Rate limits
        'daily_call_limit' => 5000,
    ],

];
