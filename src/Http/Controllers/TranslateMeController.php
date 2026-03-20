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
        $entry = $this->getEntry($data['url']);
        $defaultSite = Site::default();
        $textStrings = [];

        if (! $entry) {
            return response([
                'code'      =>  400,
                'message'   =>  'no Entry found',
            ], 400);
        }

        if ($entry->locale() === Site::default()->handle()) {
            return response([
                'code'      =>  400,
                'message'   =>  "The default language can't be translated",
            ], 400);
        }

        foreach ($data['texts'] as $text) {
            $textStrings[] = $text['html'];
        }

        try {
            $translations = $translator->translate($textStrings, $defaultSite->handle(), $entry->locale());
        } catch (\Exception $e) {
            return response()->json([
                'code'      =>  400,
                'message' => $e->getMessage(),
            ], 500);
        }

        $i = 0;
        foreach ($data['texts'] as $text) {
            $data['texts'][$i]['html'] = $translations[$i]->text;
            $i++;
        }

        return $data;
    }

    public function check(Request $request)
    {
        $data = $request->validate([
            'url' => 'required|string',
        ]);

        $entry = $this->getEntry($data['url']);
        $needTranslation = $entry && $entry->locale() !== Site::default()->handle();

        return response()->json(['need_translation' => $needTranslation]);
    }

    private function getEntry(string $url)
    {
        $segments = explode('/', trim($url, '/'));
        $id = end($segments);

        return Entry::find($id);
    }
}
