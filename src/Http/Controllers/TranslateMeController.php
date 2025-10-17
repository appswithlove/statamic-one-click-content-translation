<?php

namespace Appswithlove\StatamicOneClickContentTranslation\Http\Controllers;

use Appswithlove\StatamicOneClickContentTranslation\Interfaces\Translator;
use Illuminate\Http\Request;

class TranslateMeController
{
    public function index(Request $request, Translator $translator)
    {
        // @TODO validate request
        $data = $request->all();

        $textStrings = [];
        foreach ($data['texts'] as $text) {
            $textStrings[] = $text['html'];
        }

        try {
            $translations = $translator->translate($textStrings, $data['defaultLang'], $data['selectedLang']);
        } catch (\Error $error) {
            return response([
                'code'      =>  400,
                'message'   =>  $error->getMessage(),
            ], 400);
        }

        $i = 0;
        foreach ($data['texts'] as $text) {
            $data['texts'][$i]['html'] = $translations[$i]->text;
            $i++;
        }

        return $data;
    }
}
