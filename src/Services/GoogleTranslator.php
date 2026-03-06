<?php

namespace Appswithlove\StatamicOneClickContentTranslation\Services;

use Appswithlove\StatamicOneClickContentTranslation\Exceptions\MissingConfigurationException;
use Appswithlove\StatamicOneClickContentTranslation\Helpers\GetLocaleRegion;
use Appswithlove\StatamicOneClickContentTranslation\Interfaces\Translator;
use Google\Cloud\Translate\V3\TranslationServiceClient;

class GoogleTranslator implements Translator
{
    private string $credentialsPath;
    private string $resourceId;

    public function __construct(string $credentialsPath, string $resourceId)
    {
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

        $response = $translationClient->translateText(
            $text,
            $target,
            TranslationServiceClient::locationName($this->resourceId, 'global'),
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
