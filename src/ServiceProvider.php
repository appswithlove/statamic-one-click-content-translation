<?php

namespace Appswithlove\StatamicOneClickContentTranslation;

use Statamic\Events\EntryBlueprintFound;
use Statamic\Providers\AddonServiceProvider;
use Statamic\Statamic;

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

    public function boot()
    {
        parent::boot();

        $this->publishes([
            __DIR__.'/../config/statamic-one-click-content-translation.php' => config_path('statamic-one-click-content-translation.php'),
        ], 'statamic-one-click-content-translation-config');

        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'statamic-one-click-content-translation');

        Statamic::provideToScript([
            'oneClickTranslation' => __('statamic-one-click-content-translation::one-click'),
        ]);

        OneClickContentTranslationInputs::register();
    }
}
