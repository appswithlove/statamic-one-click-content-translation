<?php

namespace Appswithlove\StatamicOneClickContentTranslation\Helpers;

use Statamic\Facades\Site;

class GetLocaleRegion
{
    public static function getLocale($locale): string
    {
        $site = Site::all()->firstWhere('handle', $locale)->toArray();

        return $site['locale'] ?? $site['lang'] ?? $locale;
    }
}
