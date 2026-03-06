<?php

namespace Appswithlove\StatamicOneClickContentTranslation\Interfaces;

interface Translator
{
    public function translate(array $text, string $sourceLocale, string $target): string|array;
}
