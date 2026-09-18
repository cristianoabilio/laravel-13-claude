<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Supported Locales
    |--------------------------------------------------------------------------
    |
    | The locales a visitor may switch the frontend to via the header language
    | switcher. Keys must match a "lang/{key}" directory / "lang/{key}.json"
    | file. The SetLocale middleware only honors a session locale that is a
    | key in this list, so this also acts as the allow-list against arbitrary
    | locale injection.
    |
    */
    'supported' => [
        'en' => [
            'label' => 'English',
            'flag' => 'us.png',
        ],
        'pt_BR' => [
            'label' => 'Português (BR)',
            'flag' => 'br.png',
        ],
    ],
];
