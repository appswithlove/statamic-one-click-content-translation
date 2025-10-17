<?php

namespace Appswithlove\StatamicOneClickContentTranslation\Interfaces;

interface Translator
{
    public function translate(array $text, string $source, string $target): string;
}
