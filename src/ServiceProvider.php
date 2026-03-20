<?php

namespace Appswithlove\StatamicOneClickContentTranslation;

use Appswithlove\StatamicOneClickContentTranslation\Interfaces\Translator;
use Appswithlove\StatamicOneClickContentTranslation\Services\DeeplTranslator;
use Appswithlove\StatamicOneClickContentTranslation\Services\GoogleTranslator;
use Statamic\Events\EntryBlueprintFound;
use Statamic\Providers\AddonServiceProvider;

class ServiceProvider extends AddonServiceProvider
{
    protected $scripts = [
        __DIR__.'/../dist/js/statamic-one-click-content-translation.js',
    ];

    protected $stylesheets = [
        __DIR__.'/../dist/css/statamic-one-click-content-translation.css',
    ];

    protected $routes = [
        'cp' => __DIR__.'/../routes/cp.php',
    ];

    protected $listen = [
        EntryBlueprintFound::class => [
            BlueprintListener::class,
        ],
    ];

    public function register()
    {
        $this->app->bind(Translator::class, function ($app) {

            if (config('statamic-one-click-content-translation.service') === 'google') {
                return new GoogleTranslator(
                    base_path(config('statamic-one-click-content-translation.google.credentials_path')),
                    config('statamic-one-click-content-translation.google.resource_id')
                );
            }

            return new DeeplTranslator(
                config('statamic-one-click-content-translation.deepl.auth_key'),
                config('statamic-one-click-content-translation.deepl.ignore_source_lang'),
                config('statamic-one-click-content-translation.deepl.glossaries', [])
            );
        });
    }

    public function boot()
    {
        parent::boot();

        $this->publishes([
            __DIR__.'/../config/statamic-one-click-content-translation.php' => config_path('statamic-one-click-content-translation.php'),
        ], 'statamic-one-click-content-translation-config');

        OneClickContentTranslationInputs::register();
    }
}
