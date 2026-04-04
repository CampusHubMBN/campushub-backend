<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => array_filter(array_map('trim', explode(',',
        env('CORS_ALLOWED_ORIGINS',
            env('FRONTEND_URL', 'http://localhost:3000') . ',' .
            env('NESTJS_URL',   'http://localhost:3001')
        )
    ))),

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    // TOKEN AUTH: supports_credentials not required (no cookies)
    // SESSION AUTH: must be true for CSRF cookie
    'supports_credentials' => false,

];
