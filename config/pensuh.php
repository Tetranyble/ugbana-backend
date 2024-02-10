<?php

return [

    'placeholder' => [
        'profile' => [
            'path' => env('PROFILE_IMAGE', 'images/116c2708-ea98-483b-a9d0-6d85cdec4b2d-2023-08-14-11-41-55.jpeg'),
            'disk' => env('PLACEHOLDER_IMAGE_DISK', \App\Enums\StorageProvider::PUBLIC->value),
        ],
        'image' => [
            'path' => env('PLACEHOLDER_IMAGE', 'images/9085cf26-4dac-463f-ae89-07543b1319b1-2023-08-14-11-45-14.jpeg'),
            'disk' => env('PLACEHOLDER_IMAGE_DISK', \App\Enums\StorageProvider::PUBLIC->value),
        ],
        'video' => [
            'path' => env('PLACEHOLDER_VIDEO', 'https://www.youtube.com/embed/tgJBKnv1Gac'),
            'disk' => env('PLACEHOLDER_VIDEO_DISK', \App\Enums\StorageProvider::YOUTUBE->value),
        ],

    ],
    /*
        |--------------------------------------------------------------------------
        | Default number format
        |--------------------------------------------------------------------------
        |
        | This defaults will be used for the formated numbers if you don't
        | set them in the method call.
        |
        */

    'format' => [
        'format' => env('FORMAT_CURRENCY', false),

        'decimals' => 2,

        'decimal_point' => '.',

        'thousand_seperator' => ',',

    ],
    /*
    |--------------------------------------------------------------------------
    | Default tax rate
    |--------------------------------------------------------------------------
    |
    | This default tax rate will be used when you make a class implement the
    | Taxable interface and use the HasTax trait.
    |
    */

    'tax' => env('TAX_RATE', 7.5),

    'binary' => [
        'node' => env('NODE_BINARY', '/usr/bin/node'),
        'npm' => env('NPM_BINARY', '/usr/bin/npm'),
        'chrome' => env('CHROME_BINARY', '/usr/bin/google-chrome'),
    ],

];
