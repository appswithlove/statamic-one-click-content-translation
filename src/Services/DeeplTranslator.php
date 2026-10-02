<?php

namespace Appswithlove\StatamicOneClickContentTranslation\Services;

use Appswithlove\StatamicOneClickContentTranslation\Exceptions\MissingConfigurationException;
use Appswithlove\StatamicOneClickContentTranslation\Helpers\GetLocaleRegion;
use Appswithlove\StatamicOneClickContentTranslation\Interfaces\Translator;
use Illuminate\Support\Facades\Cache;

class DeeplTranslator implements Translator
{
    private const TARGET_LANGUAGES_CACHE_KEY = 'statamic-one-click-content-translation.deepl.target-languages';

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
            throw new MissingConfigurationException(__('statamic-one-click-content-translation::messages.deepl_auth_key_missing'));
        }

        $translator = new \DeepL\Translator($this->authKey);

        $target = $this->resolveTargetLang($translator, $target);

        $sourceLang = $this->ignoreSourceLang ? null : GetLocaleRegion::getLangRegion($sourceLocale)[0];

        $options = $this->setGlossaryIdFromConfig($target, $this->options);

        return $translator->translateText($text, $sourceLang, $target, $options);
    }

    private function resolveTargetLang(\DeepL\Translator $translator, string $siteHandle): string
    {
        $customLocale = GetLocaleRegion::getCustomLocale($siteHandle);

        if ($customLocale) {
            return $customLocale;
        }

        [$lang, $region] = GetLocaleRegion::getLangRegion($siteHandle);

        $supportedCodes = Cache::remember(
            self::TARGET_LANGUAGES_CACHE_KEY,
            now()->addDay(),
            fn () => array_map(fn ($language) => $language->code, $translator->getTargetLanguages())
        );

        $codesByLowercase = array_combine(array_map('strtolower', $supportedCodes), $supportedCodes);

        $exactMatch = $codesByLowercase[$region ? "{$lang}-".strtolower($region) : $lang]
            ?? $codesByLowercase[$lang]
            ?? null;

        if ($exactMatch) {
            return $exactMatch;
        }

        foreach ($codesByLowercase as $lowercaseCode => $code) {
            if (str_starts_with($lowercaseCode, "{$lang}-")) {
                return $code;
            }
        }

        return $lang;
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
