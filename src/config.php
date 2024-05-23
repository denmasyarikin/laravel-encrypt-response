<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Response
    |--------------------------------------------------------------------------
    | setup for response encryption
    | Choice one what driver for use to to encrypt
    | - cryptojs-aes
    | - ...
    */

    'response_key' => env('ENCRYPT_RESPONSE_KEY', env('APP_KEY')),
    'response_enabled' => env('ENCRYPT_RESPONSE_ENABLED', true),
    'response_optional' => env('ENCRYPT_RESPONSE_OPTIONAL', false),
    'response_header_key' => env('ENCRYPT_RESPONSE_HEADER_KEY', 'x-encrypt-response'),
    'response_driver' => env('ENCRYPT_RESPONSE_DRIVER', 'cryptojs-aes'),

    /*
    |--------------------------------------------------------------------------
    | Request
    |--------------------------------------------------------------------------
    | setup for request body decription
    */

    'request_key' => env('DECRYPT_REQUEST_KEY', env('ENCRYPT_RESPONSE_KEY', env('APP_KEY'))),
    'request_enabled' => env('DECRYPT_REQUEST_ENABLED', true),
    'request_header_key' => env('DECRYPT_REQUEST_HEADER_KEY', 'x-encrypt-request'),

    // route exception
    'route_except' => [],
];
