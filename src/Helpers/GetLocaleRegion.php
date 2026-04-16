<?php

namespace Appswithlove\StatamicOneClickContentTranslation\Helpers;

use Statamic\Facades\Site;

class GetLocaleRegion
{
    public static function getLocale($locale, $isDeepl = false): string
    {
        $site = Site::all()->firstWhere('handle', $locale)?->toArray() ?? [];

        $raw = $site['locale'] ?? $site['lang'] ?? $locale;
        [$lang, $region] = self::parse($raw);

        return $isDeepl
            ? self::forDeepl($lang, $region)
            : self::forGoogle($lang);
    }

    private static function parse(string $locale): array
    {
        $parts = preg_split('/[_\-.]/', $locale);

        return [
            strtolower($parts[0] ?? ''),
            isset($parts[1]) ? strtoupper($parts[1]) : null,
        ];
    }

    private static function forGoogle(string $lang): string
    {
        return $lang;
    }

    private static function forDeepl(string $lang, ?string $region): string
    {
        return match ($lang) {
            'en' => 'en-'.($region ?? 'US'),
            'pt' => 'pt-'.($region ?? 'PT'),
            default => $lang,
        };
    }
}
