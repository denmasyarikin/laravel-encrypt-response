<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Response
    |--------------------------------------------------------------------------
    | setup for response encryption
    */

    'response_key' => env('ENCRYPT_RESPONSE_KEY', env('APP_KEY')),
    'response_enabled' => env('ENCRYPT_RESPONSE_ENABLED', true),
    'response_optional' => env('ENCRYPT_RESPONSE_ENABLED', false),
    'response_header_key' => env('ENCRYPT_RESPONSE_HEADER_KEY', 'X-ENCRYPT-RESPONSE'),

    /*
    |--------------------------------------------------------------------------
    | Request
    |--------------------------------------------------------------------------
    | setup for request body decription
    */

    'request_key' => env('DECRYPT_REQUEST_KEY', env('ENCRYPT_RESPONSE_KEY', env('APP_KEY'))),
    'request_enabled' => env('DECRYPT_REQUEST_ENABLED', true),
    'request_optional' => env('DECRYPT_REQUEST_OPTIONAL', true),
    'request_header_key' => env('DECRYPT_REQUEST_HEADER_KEY', 'X-DECRYPT-REQUEST'),
    'request_body_key' => env('DECRYPT_REQUEST_BODY_KEY', '_payload'),

    /*
    |--------------------------------------------------------------------------
    | Encryption and decription driver
    |--------------------------------------------------------------------------
    | Choice one what driver for use to to encrypt 
    | - cryptojs-aes
    | - ...
    */
    'response_driver' => env('ENCRYPT_RESPONSE_DRIVER', 'cryptojs-aes'),
    'request_driver' => env('DECRYPT_REQUEST_DRIVER', 'cryptojs-aes'),
];
