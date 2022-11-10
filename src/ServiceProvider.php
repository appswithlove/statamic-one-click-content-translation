<?php

namespace Appswithlove\StatamicTranslateMe;


use Statamic\Providers\AddonServiceProvider;
use Statamic\Events\EntryBlueprintFound;

class ServiceProvider extends AddonServiceProvider
{
    protected $scripts = [
        __DIR__ . '/../dist/js/translate-me.js',
    ];

    protected $stylesheets = [
        __DIR__ . '/../dist/css/translate-me.css'
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

        TranslateMeInputs::register();
    }
}
