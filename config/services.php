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

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'google_maps' => [
        'api_key' => env('GOOGLE_MAPS_API_KEY'),
    ],

    'plugnotas' => [
        'base_url' => env('PLUGNOTAS_ENV') === 'production' 
            ? 'https://api.plugnotas.com.br' 
            : 'https://api.sandbox.plugnotas.com.br',
        'api_key' => env('PLUGNOTAS_TOKEN'),
        'timeout' => (int) env('PLUGNOTAS_TIMEOUT', 180),
        'mock_mode' => (bool) env('PLUGNOTAS_MOCK_MODE', false),
        'webhook_secret' => env('PLUGNOTAS_WEBHOOK_SECRET'),
    ],

];
