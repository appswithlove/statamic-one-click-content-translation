<?php

namespace Appswithlove\StatamicOneClickContentTranslation\Http\Controllers;

use Appswithlove\StatamicOneClickContentTranslation\Interfaces\Translator;
use Illuminate\Http\Request;
use Statamic\Facades\Site;

class TranslateMeController
{
    public function index(Request $request, Translator $translator)
    {
        $data = $request->validate([
            'texts' => 'required|array',
            'target' => 'required|string',
        ]);
        $defaultSite = Site::default()->handle();

        if ($data['target'] === $defaultSite) {
            return response()->json(['message' => __('statamic-one-click-content-translation::messages.default_language_not_translatable')], 400);
        }

        try {
            $translations = $translator->translate(array_column($data['texts'], 'html'), $defaultSite, $data['target']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }

        foreach ($data['texts'] as $i => $text) {
            $data['texts'][$i]['html'] = $translations[$i]->text;
        }

        return $data;
    }
}
