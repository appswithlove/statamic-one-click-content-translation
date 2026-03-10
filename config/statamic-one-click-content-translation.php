<?php

return [
    /*
    | @param array Translation options to apply. See \DeepL\TranslateTextOptions.
    */
    'deepl' => [
        'auth_key' => env('TRANSLATION_DEEPL_AUTH_KEY'),
        'formality' => 'more',
        'ignore_source_lang' => true,
        'glossaries' => [
            // 'statamic_site' => 'GLOSSARY_ID',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Translate Service
    |--------------------------------------------------------------------------
    |
    | Two options available: 'deepl' or 'google'.
    |
    */
    'service' => env('TRANSLATION_SERVICE', 'deepl'),

    /*
    |--------------------------------------------------------------------------
    | Google Cloud credential path
    |--------------------------------------------------------------------------
    |
    | They must contain 'cloudtranslate.generalModels.predict' Permission
    | Example: 'grand-object-example-key.json'. Will check file grand-object-example-key.json in project root folder
    */

    'google' => [
        'credentials_path' => env('TRANSLATION_GOOGLE_APPLICATION_CREDENTIALS'),
        'resource_id' => env('TRANSLATION_GOOGLE_APPLICATION_ID', null),
    ],
];
