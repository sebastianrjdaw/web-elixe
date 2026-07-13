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
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'xibo' => [
        'cms_url' => env('XIBO_CMS_URL'),
        'base_url' => env('XIBO_BASE_URL', rtrim((string) env('XIBO_CMS_URL'), '/').'/api'),
        'client_id' => env('XIBO_CLIENT_ID'),
        'client_secret' => env('XIBO_CLIENT_SECRET'),
        'timeout' => (int) env('XIBO_TIMEOUT', 20),
    ],

    'elixe' => [
        'leads_email' => env('ELIXE_LEADS_EMAIL', 'tecnico@elixe.es'),
        'commercial_email' => env('ELIXE_COMMERCIAL_EMAIL', 'comercial@elixe.es'),
    ],

    'turnstile' => [
        'enabled' => (bool) env('TURNSTILE_ENABLED', false),
        'site_key' => env('TURNSTILE_SITE_KEY'),
        'secret_key' => env('TURNSTILE_SECRET_KEY'),
    ],

];
