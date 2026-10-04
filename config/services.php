<?php

return [

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

    'resellerclub' => [
        'url' => env('RESELLERCLUB_API_URL'),
        'user_id' => env('RESELLERCLUB_USER_ID'),
        'api_key' => env('RESELLERCLUB_API_KEY'),
        'legacy_url' => env('RESELLERCLUB_LEGACY_API_URL', 'https://domaincheck.httpapi.com/api'),
        'dns_url' => env('RESELLERCLUB_DNS_API_URL', 'https://httpapi.com/api'),
        'dns_ttl' => env('RESELLERCLUB_DNS_TTL', 14400),
        'customer_id' => env('RESELLERCLUB_CUSTOMER_ID'),
        'contact_id' => env('RESELLERCLUB_CONTACT_ID'),
        'nameservers' => array_values(array_filter(array_map('trim', explode(',', env('RESELLERCLUB_NAMESERVERS', ''))))),
    ],

    'paystack' => [
        'secret' => env('PAYSTACK_SECRET_KEY'),
        'public' => env('PAYSTACK_PUBLIC_KEY'),
        'currency' => env('PAYSTACK_CURRENCY', 'USD'),
    ],

    'whm' => [
        'hostname' => env('WHM_HOSTNAME'),
        'username' => env('WHM_USERNAME'),
        'token' => env('WHM_API_TOKEN'),
    ],

];
