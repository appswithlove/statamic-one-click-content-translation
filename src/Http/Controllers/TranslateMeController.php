<?php

namespace Appswithlove\StatamicTranslateMe\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use DeepL\Translator;

class TranslateMeController
{
    public function index(Request $request)
    {
        // @TODO validate request
        $data = $request->all();

        $textStrings = [];
        foreach($data['texts'] as &$text) {
            $textStrings[] = $text['html'];
        }

        try {
            $translations = $this->translate($textStrings, $data['defaultLang'], $data['selectedLang']);
        } catch (\DeepL\DeepLException $error) {
            return response($error->getMessage(), 400);
        }

        foreach($data['texts'] as $key => $text) {
            $data['texts'][$key]['html'] = $translations[$key]->text;
        }

        return $data;
    }

    private function translate(array $texts, string $fromLang, string $toLang) {
        $authKey = config('translate-me.deepl_auth_key');
        $translator = new Translator($authKey);

        switch($toLang) {
            case 'en':
                $toLang = config('translate-me.target_lang_for_en');
                break;
        }

        $translations = $translator->translateText($texts, $fromLang, $toLang);

        return $translations;
    }
}
