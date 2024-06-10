<?php

namespace Appswithlove\StatamicOneClickContentTranslation\Http\Controllers;

use Illuminate\Http\Request;
use DeepL\Translator;
use Google\Cloud\Translate\V3\TranslationServiceClient;

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

        if (config('statamic-one-click-content-translation.service') === 'google') {
            try {
                $translations = $this->googleTranslate($textStrings, $data['defaultLang'], $data['selectedLang']);
            } catch (\Error $error) {
                return response([
                    'code'      =>  400,
                    'message'   =>  $error->getMessage()
                ], 400);
            }
        } else {
            try {
                $translations = $this->deeplTranslate($textStrings, $data['defaultLang'], $data['selectedLang']);
            } catch (\DeepL\DeepLException $error) {
                return response([
                    'code'      =>  400,
                    'message'   =>  $error->getMessage()
                ], 400);
            }
        }

        $i = 0;
        foreach($data['texts'] as $text) {
            $data['texts'][$i]['html'] = $translations[$i]->text;
            $i++;
        }

        return $data;
    }

    private function deeplTranslate(array $texts, string $fromLang, string $toLang) {
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
        $options = config('statamic-one-click-content-translation.deepl_options', []);
        
        $options = $this->setGlossaryIdFromConfig($toLang, $options);

        $translations = $translator->translateText($texts, $sourceLang, $toLang, $options);

        return $translations;
    }

    private function googleTranslate(array $texts, string $fromLang, string $toLang)
    {
        $credentialsPath = base_path(config('statamic-one-click-content-translation.google_credetials'));
        if (!$credentialsPath) {
            throw new \Exception('Empty Google Cloud credentials');
        }

        if (!file_exists($credentialsPath)) {
            throw new \Exception("Can't open file with credentials: $credentialsPath");
        }

        $googleResourceId = config('statamic-one-click-content-translation.google_resource_id');

        if (!$googleResourceId) {
            throw new \Exception('Google Cloud resource ID empty');
        }

        $translationClient = new TranslationServiceClient([
            'credentials' => $credentialsPath
        ]);

        $response = $translationClient->translateText(
            $texts,
            $toLang,
            TranslationServiceClient::locationName($googleResourceId, 'global'),
            [
                'mimeType' => 'text/html',
            ],
        );

        $translatedTexts = [];
        foreach ($response->getTranslations() as $translation) {
            $translatedTexts[] = (object) ['text' => $translation->getTranslatedText()];
        }

        return $translatedTexts;
    }

    /**
     * If a 'glossaries' key is supplied to the DeepL-options in config, get the correct one for the Statamic site.
     *
     * @param string $toLang
     * @param array $options
     *
     * @return array
     */
    private function setGlossaryIdFromConfig(string $toLang, array $options): array
    {
        if (array_key_exists('glossary', $options)) {
            // If there is already a 'glossary_id' set in options, we don't want to overwrite it, do nothing and return options.
            return $options;
        }

        if (!array_key_exists('glossaries', $options)) {
            // If there is no 'glossaries' option defined, do nothing and return options.
            return $options;
        }

        $glossaries = $options['glossaries'];
        if (!array_key_exists($toLang, $glossaries)) {
            // There is no glossary_id supplied for the current toLang, do nothing and return options.
            return $options;
        }

        // We know there is a glossary_id for the current $toLang, set that as the glossary_id key in options.
        $options['glossary'] = $glossaries[$toLang];

        // Unset the 'glossaries' key in options since it is no longer needed.
        unset($options['glossaries']);

        // Return '$options' which now contains the correct glossary_id for the current Statamic site.
        return $options;
    }
}
