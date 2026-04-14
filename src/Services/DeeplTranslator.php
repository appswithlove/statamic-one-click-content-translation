<?php

namespace Appswithlove\StatamicOneClickContentTranslation\Services;

use Appswithlove\StatamicOneClickContentTranslation\Exceptions\MissingConfigurationException;
use Appswithlove\StatamicOneClickContentTranslation\Helpers\GetLocaleRegion;
use Appswithlove\StatamicOneClickContentTranslation\Interfaces\Translator;

class DeeplTranslator implements Translator
{
    private string $authKey;
    private bool $ignoreSourceLang;
    private array $options;

    public function __construct(string $authKey, bool $ignoreSourceLang = false, array $options = [])
    {
        $this->authKey = $authKey;
        $this->ignoreSourceLang = $ignoreSourceLang;
        $this->options = $options;
    }

    public function translate(array $text, string $sourceLocale, string $target): string|array
    {
        if (! $this->authKey) {
            throw new MissingConfigurationException('Empty Deepl Auth Key');
        }

        $translator = new \DeepL\Translator($this->authKey);

        $target = GetLocaleRegion::getLocale($target, true);
        $sourceLocale = GetLocaleRegion::getLocale($sourceLocale, true);

        $sourceLang = $this->ignoreSourceLang ? null : $sourceLocale;

        $options = $this->setGlossaryIdFromConfig($target, $this->options);

        return $translator->translateText($text, $sourceLang, $target, $options);
    }

    private function setGlossaryIdFromConfig(string $toLang, array $options): array
    {
        if (array_key_exists('glossary', $options)) {
            return $options;
        }

        if (! array_key_exists('glossaries', $options)) {
            return $options;
        }

        $glossaries = $options['glossaries'];

        if (! array_key_exists($toLang, $glossaries)) {
            return $options;
        }

        $options['glossary'] = $glossaries[$toLang];
        unset($options['glossaries']);

        return $options;
    }
}
