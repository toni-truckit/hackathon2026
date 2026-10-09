<?php

declare(strict_types=1);

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

    'truckit_partner' => [
        // no default URL on purpose: point at the sandbox in .env, never prod by accident
        'base_url' => env('TRUCKIT_PARTNER_BASE_URL'),
        'auth_url' => env('TRUCKIT_PARTNER_AUTH_URL', 'https://auth-au.truckit.net/oauth2/token'),
        'client_id' => env('TRUCKIT_PARTNER_CLIENT_ID'),
        'client_secret' => env('TRUCKIT_PARTNER_CLIENT_SECRET'),
        // x-customer-* headers on quote calls; these are not the OAuth client id/secret
        'customer_id' => env('TRUCKIT_PARTNER_CUSTOMER_ID'),
        'customer_secret' => env('TRUCKIT_PARTNER_CUSTOMER_SECRET', env('TRUCKIT_PARTNER_CLIENT_SECRET')),
        'api_key' => env('TRUCKIT_PARTNER_API_KEY'),
        'scope' => env('TRUCKIT_PARTNER_SCOPE', 'partner-api/get-quote'),
        'timeout' => 20,
    ],

];
