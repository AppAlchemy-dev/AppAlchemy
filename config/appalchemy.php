<?php

return [
    /*
    |--------------------------------------------------------------------------
    | AppAlchemy User Agent
    |--------------------------------------------------------------------------
    |
    | This value is used to identify requests coming from the AppAlchemy app.
    | You can customize this if you want to use a different identifier.
    |
    */
    'user_agent' => 'AppAlchemy',

    /*
    |--------------------------------------------------------------------------
    | AppAlchemy Styles
    |--------------------------------------------------------------------------
    |
    | Define any global styles to be applied when the app is detected.
    |
    */
    'styles' => [
        'body' => 'appalchemy-body',
        'content' => 'appalchemy-content',
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Settings
    |--------------------------------------------------------------------------
    |
    | Configure authentication-related settings for AppAlchemy.
    |
    */
    'auth' => [
        // The authentication guard to use for web sessions
        'guard' => 'web',

        // The user provider to use for retrieving users
        'provider' => 'users',

        // The field in the user model that stores the API token
        'token_field' => 'api_token',

        // The header name for the API token
        'header' => 'X-AppAlchemy-Auth-Token',
    ],
];
