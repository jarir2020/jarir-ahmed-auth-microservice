<?php

return [

    /*
    |--------------------------------------------------------------------------
    | User Model
    |--------------------------------------------------------------------------
    */
    'user_model' => \App\Models\User::class,

    /*
    |--------------------------------------------------------------------------
    | Registration
    |--------------------------------------------------------------------------
    */
    'registration' => [
        'require_email_verification' => true,
        'email_verification_expires_minutes' => 60,
    ],

    /*
    |--------------------------------------------------------------------------
    | Login
    |--------------------------------------------------------------------------
    */
    'login' => [
        'remember_me_days' => 30,
    ],

    /*
    |--------------------------------------------------------------------------
    | Magic Link
    |--------------------------------------------------------------------------
    */
    'magic_link' => [
        'expires_minutes' => 15,
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Reset
    |--------------------------------------------------------------------------
    */
    'password_reset' => [
        'expires_minutes' => 60,
    ],

    /*
    |--------------------------------------------------------------------------
    | Account Lockout
    |--------------------------------------------------------------------------
    */
    'lockout' => [
        'max_attempts' => 5,
        'lockout_minutes' => 15,
    ],

    /*
    |--------------------------------------------------------------------------
    | Two-Factor Authentication
    |--------------------------------------------------------------------------
    */
    'two_factor' => [
        'issuer' => env('APP_NAME', 'AuthMicroservice'),
        'backup_codes_count' => 8,
    ],

    /*
    |--------------------------------------------------------------------------
    | API Tokens
    |--------------------------------------------------------------------------
    */
    'tokens' => [
        'default_expiry_days' => 365,
    ],

    /*
    |--------------------------------------------------------------------------
    | Social Login (OAuth)
    |--------------------------------------------------------------------------
    */
    'oauth' => [
        'google' => [
            'client_id'     => env('GOOGLE_CLIENT_ID'),
            'client_secret' => env('GOOGLE_CLIENT_SECRET'),
            'redirect_uri'  => env('GOOGLE_REDIRECT_URI'),
        ],
        'facebook' => [
            'client_id'     => env('FACEBOOK_CLIENT_ID'),
            'client_secret' => env('FACEBOOK_CLIENT_SECRET'),
            'redirect_uri'  => env('FACEBOOK_REDIRECT_URI'),
        ],
        'github' => [
            'client_id'     => env('GITHUB_CLIENT_ID'),
            'client_secret' => env('GITHUB_CLIENT_SECRET'),
            'redirect_uri'  => env('GITHUB_REDIRECT_URI'),
        ],
        'twitter' => [
            'client_id'     => env('TWITTER_CLIENT_ID'),
            'client_secret' => env('TWITTER_CLIENT_SECRET'),
            'redirect_uri'  => env('TWITTER_REDIRECT_URI'),
        ],
        'linkedin' => [
            'client_id'     => env('LINKEDIN_CLIENT_ID'),
            'client_secret' => env('LINKEDIN_CLIENT_SECRET'),
            'redirect_uri'  => env('LINKEDIN_REDIRECT_URI'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Tracking
    |--------------------------------------------------------------------------
    */
    'tracking' => [
        'geolocation_api' => env('GEOLOCATION_API_URL', 'http://ip-api.com/json'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Notifications
    |--------------------------------------------------------------------------
    */
    'notifications' => [
        'new_device_login'  => true,
        'password_change'   => true,
        'two_factor_toggle' => true,
        'suspicious_login'  => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | JWT
    |--------------------------------------------------------------------------
    */
    'jwt' => [
        'secret'     => env('AUTH_JWT_SECRET', 'change-me'),
        'algorithm'  => 'HS256',
        'expires_in' => 3600,
    ],

    /*
    |--------------------------------------------------------------------------
    | Routes
    |--------------------------------------------------------------------------
    */
    'routes' => [
        'prefix'     => 'auth',
        'middleware' => ['web'],
    ],

];
