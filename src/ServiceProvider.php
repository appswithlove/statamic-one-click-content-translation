<?php

namespace Appswithlove\StatamicOneClickContentTranslation;


use Statamic\Providers\AddonServiceProvider;
use Statamic\Events\EntryBlueprintFound;

class ServiceProvider extends AddonServiceProvider
{
    protected $scripts = [
        __DIR__ . '/../dist/js/statamic-one-click-content-translation.js',
    ];

    protected $stylesheets = [
        __DIR__ . '/../dist/css/statamic-one-click-content-translation.css'
    ];

    protected $routes = [
        'cp' => __DIR__ . '/../routes/cp.php',
    ];

    protected $listen = [
        EntryBlueprintFound::class => [
            BlueprintListener::class,
        ],
    ];

    public function boot()
    {
        parent::boot();

        OneClickContentTranslationInputs::register();
    }
}
