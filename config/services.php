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
    'apilayer' => [
        'key' => env('API_LAYER_KEY'),
        'url' => 'https://api.apilayer.com/resume_parser/'
    ],
    'github' => [
        'client_id' => env('GITHUB_CLIENT_ID'),
        'client_secret' => env('GITHUB_CLIENT_SECRET'),
        'redirect' => env('APP_URL').'/api/v1/login/github',
    ],
    'facebook' => [
        'client_id' => env('FACEBOOK_CLIENT_ID'),
        'client_secret' => env('FACEBOOK_CLIENT_SECRET'),
        'redirect' => env('APP_URL').'/api/v1/login/facebook',
    ],
    'twitter' => [
        'client_id' => env('TWITTER_CLIENT_ID'),
        'client_secret' => env('TWITTER_CLIENT_SECRET'),
        'redirect' => env('APP_URL').'/api/v1/login/twitter',
    ],
    'linkedin' => [
        'client_id' => env('LINKEDIN_CLIENT_ID'),
        'client_secret' => env('LINKEDIN_CLIENT_SECRET'),
        'redirect' => env('APP_URL').'/api/v1/login/linkedin',
    ],
    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('APP_URL').'/api/v1/login/google',
        //'service_redirect' => env('SERVICE_REDIRECT'),
        'service_redirect' => env('APP_URL').'/services/authorization/google',
        'scope' => [
//            'https://www.googleapis.com/auth/drive',
//            'https://www.googleapis.com/auth/drive.file',
            'https://www.googleapis.com/auth/userinfo.email',
            'https://www.googleapis.com/auth/userinfo.profile',
//            'https://www.googleapis.com/auth/calendar',
//            'https://www.googleapis.com/auth/calendar.events',

            'https://www.googleapis.com/auth/youtube',
            'https://www.googleapis.com/auth/youtube.upload',
            'https://www.googleapis.com/auth/youtube.readonly',
            'https://www.googleapis.com/auth/youtube.channel-memberships.creator',
//
//            'https://www.googleapis.com/auth/youtubepartner',
            //'https://www.googleapis.com/auth/youtube.force-ssl',
        ],
        // Enables automatic token refresh.
        'approval_prompt' => 'force',
        'access_type' => 'offline',

        // Enables incremental scopes (useful if in the future we need access to another type of data).
        'include_granted_scopes' => true,
    ],

    'mailchimp' => [
        'api_key' => env('MAILCHIMP_API_KEY'),
        'server' => env('MAILCHIMP_SERVER_PREFIX'),
        'list_id' => env('MAILCHIMP_LIST_ID'),
    ],
    'cloudinary' => [
        'cloud_name' => env('CLOUDINARY_NAME', 'dpknwt0hp'),
        'api_key' => env('CLOUDINARY_API_KEY'),
        'api_secret' => env('CLOUDINARY_API_SECRET'),
    ],
    'vimeo' => [
        'client_id' => env('VIMEO_CLIENT_ID'),
        'client_secret' => env('VIMEO_CLIENT_SECRET'),
        'access_token' => env('VIMEO_ACCESS_TOKEN'),
    ],

    'twilio' => [
        'verify_sid' => env('TWILIO_VERIFY_SID'),
        'sid' => env('TWILIO_SID'),
        'token' => env('TWILIO_TOKEN'),
        'sender' => env('TWILIO_SENDER'),
    ],
    'braincert' => [
        'key' => env('BRAINCERT_KEY'),
        'url' => env('BRAINCERT_URL'),
    ],
    'stripe' => [
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
        'trial' => env('STRIPE_TRIAL', 7),
    ],
    'sendbird' => [
        'token' => env('SENDBIRD_TOKEN'),
        'url' => env('SENDBIRD_URL'),
    ],

];
