<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Test Token
    |--------------------------------------------------------------------------
    |
    | It's need for developer model, for fast testing API (postman and etc.)
    |
    */
    'test_token' => env('TEST_TOKEN', null),

    /*
    |--------------------------------------------------------------------------
    | JWT hashing algorithm
    |--------------------------------------------------------------------------
    |
    | Specify the hashing algorithm that will be used to sign the token.
    |
    | See here: https://github.com/namshi/jose/tree/master/src/Namshi/JOSE/Signer/OpenSSL
    | for possible values.
    |
    */
    'algo' => env('JWT_ALGO', 'sha256'),

    /*
    |--------------------------------------------------------------------------
    | User model path
    |--------------------------------------------------------------------------
    */
    'user_model' => App\Models\User::class,

    /*
    |--------------------------------------------------------------------------
    | Get user function for auth in develop mode, when you test_token available
    |--------------------------------------------------------------------------
    |
    | It can be empty
    */
    'get_test_auth_user_func' => function () {
        return App\Models\User::first();
    },

    /*
    |--------------------------------------------------------------------------
    | Sub field for payload
    |--------------------------------------------------------------------------
    */
    'sub_payload_field' => 'id',

    /*
    |--------------------------------------------------------------------------
    | Name fields for payload
    |--------------------------------------------------------------------------
    */
    'name_payload_fields' => ['phone', 'email'],

    /*
    |--------------------------------------------------------------------------
    | JWT time to live
    |--------------------------------------------------------------------------
    |
    | Specify the length of time (in minutes) that the token will be valid for.
    | Defaults to 1 hour.
    */
    'ttl' => env('JWT_TTL', 3600),

    /*
    |--------------------------------------------------------------------------
    | Refresh time to live
    |--------------------------------------------------------------------------
    |
    | Specify the length of time (in minutes) that the token can be refreshed
    | within. I.E. The user can refresh their token within a 2 week window of
    | the original token being created until they must re-authenticate.
    | Defaults to 2 weeks.
    */
    'refresh_ttl' => env('JWT_REFRESH_TTL', 20160),

    /*
    |--------------------------------------------------------------------------
    | Generate jwt token without time
    |--------------------------------------------------------------------------
    */
    'allow_infinite_ttl' => false,

    /*
    |--------------------------------------------------------------------------
    | Infinity ttl time to live
    |--------------------------------------------------------------------------
    */
    'infinite_ttl_fallback' => 31536000,

    /*
    |--------------------------------------------------------------------------
    | Info about blacklist storage
    |--------------------------------------------------------------------------
    */
    'token_storage' => [
        /*
        |--------------------------------------------------------------------------
        | Which drive use (database/redis)
        |--------------------------------------------------------------------------
        */
        'driver' => env('JWT_TOKEN_STORAGE_DRIVER', 'database'),

        /*
        |--------------------------------------------------------------------------
        | How long we need to save this token
        |--------------------------------------------------------------------------
        */
        'storage_ttl' => env('JWT_BLACKLIST_STORAGE_TTL', 86400 * 7),
    ],
];
