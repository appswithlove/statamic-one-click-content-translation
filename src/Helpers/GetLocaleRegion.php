<?php

namespace Appswithlove\StatamicOneClickContentTranslation\Helpers;

use Statamic\Facades\Site;

class GetLocaleRegion
{
    public const CUSTOM_LOCALE_ATTRIBUTE = 'translation_locale';

    public static function getCustomLocale(string $handle): ?string
    {
        return Site::get($handle)?->attribute(self::CUSTOM_LOCALE_ATTRIBUTE) ?: null;
    }

    /**
     * @return array{0: string, 1: ?string} [lang, region]
     */
    public static function getLangRegion(string $handle): array
    {
        $site = Site::get($handle);

        return self::parse(self::getCustomLocale($handle) ?? $site?->locale() ?? $site?->lang() ?? $handle);
    }

    /**
     * @return array{0: string, 1: ?string} [lang, region]
     */
    private static function parse(string $locale): array
    {
        $parts = preg_split('/[_\-.]/', $locale);

        return [
            strtolower($parts[0] ?? ''),
            isset($parts[1]) ? strtoupper($parts[1]) : null,
        ];
    }
}
