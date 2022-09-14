<?php

namespace Appswithlove\StatamicTranslateMe\Http\Controllers;

use Illuminate\Http\Request;
use DeepL\Translator;

class TranslateMeController
{
    public function index(Request $request)
    {
        // @TODO validate request
        $data = $request->all();

        $textStrings = [];
        foreach($data['texts'] as $text) {
            $textStrings[] = $text['html'];
        }

        try {
            $translations = $this->translate($textStrings, $data['defaultLang'], $data['selectedLang']);
        } catch (\DeepL\DeepLException $error) {
            return response($error->getMessage(), 400);
        }

        $i = 0;
        foreach($data['texts'] as $text) {
            $data['texts'][$i]['html'] = $translations[$i]->text;
            $i++;
        }

        return $data;
    }

    private function translate(array $texts, string $fromLang, string $toLang) {
        $authKey = config('statamic-translate-me.deepl_auth_key');
        $translator = new Translator($authKey);

        switch($toLang) {
            case 'en':
                $toLang = config('statamic-translate-me.target_lang_for_en');
                break;
        }

        $translations = $translator->translateText($texts, $fromLang, $toLang);

        return $translations;
    }
}
