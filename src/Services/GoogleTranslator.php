<?php

namespace Appswithlove\StatamicOneClickContentTranslation\Services;

use Appswithlove\StatamicOneClickContentTranslation\Exceptions\MissingConfigurationException;
use Appswithlove\StatamicOneClickContentTranslation\Helpers\GetLocaleRegion;
use Appswithlove\StatamicOneClickContentTranslation\Interfaces\Translator;
use Google\Cloud\Translate\V3\Client\TranslationServiceClient;
use Google\Cloud\Translate\V3\TranslateTextRequest;

class GoogleTranslator implements Translator
{
    private string $credentialsPath;
    private string $resourceId;

    public function __construct(?string $credentialsPath, ?string $resourceId)
    {
        if (empty($credentialsPath) || ! file_exists($credentialsPath)) {
            throw new MissingConfigurationException(
                "Google credentials file is missing or cannot be opened: {$credentialsPath}"
            );
        }

        if (empty($resourceId)) {
            throw new MissingConfigurationException(
                'TRANSLATION_GOOGLE_APPLICATION_ID is missing'
            );
        }

        $this->credentialsPath = $credentialsPath;
        $this->resourceId = $resourceId;
    }

    public function translate(array $text, string $sourceLocale, string $target): string|array
    {
        if (! file_exists($this->credentialsPath)) {
            throw new MissingConfigurationException("Can't open file with credentials: {$this->credentialsPath}");
        }

        $translationClient = new TranslationServiceClient([
            'credentials' => $this->credentialsPath,
        ]);

        $target = GetLocaleRegion::getLocale($target);

        $request = new TranslateTextRequest([
            'parent' => $translationClient->locationName($this->resourceId, 'global'),
            'contents' => $text,
            'target_language_code' => $target,
            'mime_type' => 'text/html',
        ]);

        $response = $translationClient->translateText($request);

        $translatedTexts = [];
        foreach ($response->getTranslations() as $translation) {
            $translatedTexts[] = (object) ['text' => $translation->getTranslatedText()];
        }

        return $translatedTexts;
    }
}
