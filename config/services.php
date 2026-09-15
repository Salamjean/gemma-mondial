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

    'agora' => [
        'app_id' => env('AGORA_APP_ID', 'f502a4bc7cd84daeba1072d3bf48b1f3'),
        'app_certificate' => env('AGORA_APP_CERTIFICATE', 'c6005f2c17ed47309f4f4b21356f6e37'),
    ],

    'yellika' => [
        'api_url' => env('YELLIKA_API_URL', 'https://app.1smsafrica.com/api/v3'),
        'api_key' => env('YELLIKA_API_KEY', '811|BjTyHTb8CdJnYA6nBDhiaX9R0f3gtXG9FTKaZpGKf035957f'),
        'sender_id' => env('YELLIKA_SENDER_ID', 'GestPatient'),
    ],

    'livekit' => [
        'url' => env('LIVEKIT_URL', 'wss://gemma-14fckk2m.livekit.cloud'),
        'api_key' => env('LIVEKIT_API_KEY', 'APIVdAJ5HfTwJEi'),
        'api_secret' => env('LIVEKIT_API_SECRET', 'yFs8UoilwvwcuMeYUi6coYMADMSEfaVJnpfNlY1X7HUB'),
    ],

    'wave' => [
        'api_key' => env('WAVE_API_KEY'),
        'webhook_secret' => env('WAVE_WEBHOOK_SECRET'),
        'base_url' => 'https://api.wave.com/v1',
    ],

];
