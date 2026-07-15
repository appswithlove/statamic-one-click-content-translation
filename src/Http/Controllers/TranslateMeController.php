<?php

namespace Appswithlove\StatamicOneClickContentTranslation\Http\Controllers;

use Appswithlove\StatamicOneClickContentTranslation\Interfaces\Translator;
use Illuminate\Http\Request;
use Statamic\Facades\Entry;
use Statamic\Facades\GlobalSet;
use Statamic\Facades\Site;

class TranslateMeController
{
    public function index(Request $request, Translator $translator)
    {
        $data = $request->validate([
            'texts' => 'required|array',
            'url'   => 'required|string',
            'lang'  => 'nullable|string',
        ]);
        $defaultSite = Site::default();
        $textStrings = [];

        $targetLocale = $data['lang'] ?? null;

        if (! $targetLocale) {
            $localizable = $this->getLocalizable($data['url']);

            if (! $localizable) {
                return response([
                    'code'      =>  400,
                    'message'   =>  'no Entry or Global Set found',
                ], 400);
            }

            if ($localizable->locale() === $defaultSite->handle()) {
                return response([
                    'code'      =>  400,
                    'message'   =>  "The default language can't be translated",
                ], 400);
            }

            $targetLocale = $localizable->locale();
        }

        foreach ($data['texts'] as $text) {
            $textStrings[] = $text['html'];
        }

        try {
            $translations = $translator->translate($textStrings, $defaultSite->handle(), $targetLocale);
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

        $localizable = $this->getLocalizable($data['url']);
        $needTranslation = $localizable && $localizable->locale() !== Site::default()->handle();

        return response()->json(['need_translation' => $needTranslation]);
    }

    private function getLocalizable(string $url)
    {
        $path = parse_url($url, PHP_URL_PATH) ?? $url;
        $segments = explode('/', trim($path, '/'));
        $id = end($segments);

        if ($entry = Entry::find($id)) {
            return $entry;
        }

        if ($globalSet = GlobalSet::find($id)) {
            parse_str(parse_url($url, PHP_URL_QUERY) ?? '', $query);

            return $globalSet->in($query['site'] ?? Site::selected()->handle());
        }

        return null;
    }
}
