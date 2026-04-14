<?php

namespace Appswithlove\StatamicOneClickContentTranslation\Helpers;

use Statamic\Facades\Site;

class GetLocaleRegion
{
    public static function getLocale($locale, $isDeepl = false): string
    {
        $site = Site::all()->firstWhere('handle', $locale)->toArray();

        if ($isDeepl) {
            $raw = $site['locale'] ?? $site['lang'] ?? $locale;
            $parts = preg_split('/[_\-.]/', $raw);
            $lang = strtolower($parts[0] ?? '');
            $region = isset($parts[1]) ? strtoupper($parts[1]) : null;

            return $region ? "{$lang}-{$region}" : $lang;
        }

        return preg_split('/[_-]/', $site['locale'] ?? $site['lang'] ?? $locale)[0];
    }
}
