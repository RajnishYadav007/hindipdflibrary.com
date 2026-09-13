<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Supported Locales
    |--------------------------------------------------------------------------
    |
    | A list of locales officially supported by the platform.
    | Adding new languages (e.g., 'es', 'fr', 'sa') in the future requires
    | only adding an entry here without architectural changes.
    |
    */

    'supported_locales' => [
        'en' => [
            'name' => 'English',
            'native' => 'English',
            'label' => 'English',
            'regional' => 'en_US',
            'dir' => 'ltr',
        ],
        'hi' => [
            'name' => 'हिन्दी',
            'native' => 'हिन्दी',
            'label' => 'हिन्दी',
            'regional' => 'hi_IN',
            'dir' => 'ltr',
        ],
        'sa' => [
            'name' => 'संस्कृतम्',
            'native' => 'संस्कृतम्',
            'label' => 'संस्कृतम्',
            'regional' => 'sa_IN',
            'dir' => 'ltr',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Locale
    |--------------------------------------------------------------------------
    |
    | The primary locale for the site.
    |
    */

    'default_locale' => env('APP_LOCALE', 'en'),

    /*
    |--------------------------------------------------------------------------
    | Fallback Locale
    |--------------------------------------------------------------------------
    |
    | Used when a requested entity translation is missing.
    |
    */

    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),

];
