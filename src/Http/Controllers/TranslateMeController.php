<?php

namespace Appswithlove\StatamicOneClickContentTranslation\Http\Controllers;

use Appswithlove\StatamicOneClickContentTranslation\Interfaces\Translator;
use Illuminate\Http\Request;
use Statamic\Facades\Entry;
use Statamic\Facades\Site;

class TranslateMeController
{
    public function index(Request $request, Translator $translator)
    {
        $data = $request->validate([
            'texts' => 'required|array',
            'url'   => 'required|string',
        ]);
        $segments = explode('/', trim($data['url'], '/'));
        $id = end($segments);
        $entry = Entry::find($id);
        $defaultSite = Site::default();
        $textStrings = [];
        if (! $entry) {
            return response([
                'code'      =>  400,
                'message'   =>  'no Entry found',
            ], 400);
        }
        foreach ($data['texts'] as $text) {
            $textStrings[] = $text['html'];
        }

        try {
            $translations = $translator->translate($textStrings, $defaultSite->handle(), $entry->locale());
        } catch (\Exception $error) {
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
