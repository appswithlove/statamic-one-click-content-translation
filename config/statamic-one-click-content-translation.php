<?php

return [
    /*
    | @param array Translation options to apply. See \DeepL\TranslateTextOptions.
    */
    'deepl' => [
        'auth_key' => env('DEEPL_AUTH_KEY'),
        'glossaries' => [
            // 'statamic_site' => 'GLOSSARY_ID',
        ],
    ],
    /*
     * Translate into Lang with 'less' and 'more' Formality:
     */
    'formality' => 'more',
    'ignore_source_lang' => true,

    /*
    |--------------------------------------------------------------------------
    | Translate Service
    |--------------------------------------------------------------------------
    |
    | Two options available: 'deepl' or 'google'.
    |
    */
    'service' => env('ONE_CLICK_CONTENT_TRANSLATION_SERVICE', 'deepl'),

    /*
    |--------------------------------------------------------------------------
    | Google Cloud credential path
    |--------------------------------------------------------------------------
    |
    | They must contain 'cloudtranslate.generalModels.predict' Permission
    | Example: 'grand-object-example-key.json'. Will check file grand-object-example-key.json in project root folder
    */

    'google' => [
        'auth_key' => env('GOOGLE_AUTH_KEY'),
        'resource_id' => env('ONE_CLICK_CONTENT_TRANSLATION_GOOGLE_APPLICATION_ID', null),
    ],
];
