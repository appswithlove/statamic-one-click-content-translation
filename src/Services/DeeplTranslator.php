<?php

namespace Appswithlove\StatamicOneClickContentTranslation\Services;

use Appswithlove\StatamicOneClickContentTranslation\Helpers\GetLocaleRegion;
use Appswithlove\StatamicOneClickContentTranslation\Interfaces\Translator;

class DeeplTranslator implements Translator
{
    public function translate(array $text, string $source, string $target): string
    {
        $authKey = config('statamic-one-click-content-translation.deepl.auth_key');
        if (! $authKey) {
            throw new \Exception('Empty Deepl Auth Key');
        }

        $translator = new \DeepL\Translator($authKey);

        $target = GetLocaleRegion::getLocale($target);

        $sourceLang = config('statamic-one-click-content-translation.ignore_source_lang') ? null : $source;
        $options = config('statamic-one-click-content-translation.deepl.glossaries', []);

        $options = $this->setGlossaryIdFromConfig($target, $options);

        $translations = $translator->translateText($text, $sourceLang, $target, $options);

        return $translations;
    }

    /**
     * If a 'glossaries' key is supplied to the DeepL-options in config, get the correct one for the Statamic site.
     */
    private function setGlossaryIdFromConfig(string $toLang, array $options): array
    {
        if (array_key_exists('glossary', $options)) {
            // If there is already a 'glossary_id' set in options, we don't want to overwrite it, do nothing and return options.
            return $options;
        }

        if (! array_key_exists('glossaries', $options)) {
            // If there is no 'glossaries' option defined, do nothing and return options.
            return $options;
        }

        $glossaries = $options['glossaries'];
        if (! array_key_exists($toLang, $glossaries)) {
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
