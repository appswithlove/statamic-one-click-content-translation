<?php

namespace Appswithlove\StatamicOneClickContentTranslation\Http\Controllers;

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
            return response([
                'code'      =>  400,
                'message'   =>  $error->getMessage()
            ], 400);
        }

        $i = 0;
        foreach($data['texts'] as $text) {
            $data['texts'][$i]['html'] = $translations[$i]->text;
            $i++;
        }

        return $data;
    }

    private function translate(array $texts, string $fromLang, string $toLang) {
        $authKey = config('statamic-one-click-content-translation.deepl_auth_key');
        if (!$authKey) {
            throw new \Exception('Empty Deepl Auth Key');
        }

        $translator = new Translator($authKey);

        switch($toLang) {
            case 'en':
                $toLang = config('statamic-one-click-content-translation.target_lang_for_en');
                break;
            case 'pt':
                $toLang = config('statamic-one-click-content-translation.target_lang_for_pt');
                break;
        }

        $sourceLang =  config('statamic-one-click-content-translation.ignore_source_lang') ? null : $fromLang;

        $translations = $translator->translateText($texts, $sourceLang, $toLang);

        return $translations;
    }
}
