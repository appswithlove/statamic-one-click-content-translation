<?php

return [

    'deepl_auth_key' => env('DEEPL_AUTH_KEY'),

    /*
     * Translate into Lang with 'less' and 'more' Formality:
     */
    'formality' => 'more',
    'target_lang_for_en' => 'en-GB',
    'target_lang_for_pt' => 'pt-PT',
    'ignore_source_lang' => false,

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
    */
    'google_credetials' => env('ONE_CLICK_CONTENT_TRANSLATION_GOOGLE_APPLICATION_CREDENTIALS', null),

    /*
    |--------------------------------------------------------------------------
    | Google Cloud credential path
    |--------------------------------------------------------------------------
    |
    | Example: 'grand-object-example-key.json'. Will check file grand-object-example-key.json in project root folder
    */
    'google_resource_id' => env('ONE_CLICK_CONTENT_TRANSLATION_GOOGLE_APPLICATION_ID', null),

];
