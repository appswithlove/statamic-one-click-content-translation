<?php

namespace Appswithlove\StatamicOneClickContentTranslation\Services;

use Appswithlove\StatamicOneClickContentTranslation\Helpers\GetLocaleRegion;
use Appswithlove\StatamicOneClickContentTranslation\Interfaces\Translator;
use Google\Cloud\Translate\V3\TranslationServiceClient;

class GoogleTranslator implements Translator
{
    public function translate(array $text, string $source, string $target): string
    {
        $credentialsPath = base_path(config('statamic-one-click-content-translation.google.auth_key'));
        if (! $credentialsPath) {
            throw new \Exception('Empty Google Cloud credentials');
        }

        if (! file_exists($credentialsPath)) {
            throw new \Exception("Can't open file with credentials: $credentialsPath");
        }

        $googleResourceId = config('statamic-one-click-content-translation.google.resource_id');

        if (! $googleResourceId) {
            throw new \Exception('Google Cloud resource ID empty');
        }

        $translationClient = new TranslationServiceClient([
            'credentials' => $credentialsPath,
        ]);

        $target = GetLocaleRegion::getLocale($target);

        $response = $translationClient->translateText(
            $text,
            $target,
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
}
