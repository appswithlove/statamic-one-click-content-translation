<?php

namespace Appswithlove\StatamicOneClickContentTranslation;

use Statamic\Events\AssetContainerBlueprintFound;
use Statamic\Events\EntryBlueprintFound;
use Statamic\Events\GlobalVariablesBlueprintFound;
use Statamic\Facades\User;

class BlueprintListener
{
    public function handle(EntryBlueprintFound|GlobalVariablesBlueprintFound|AssetContainerBlueprintFound $event)
    {
        if (! User::current()) {
            return;
        }

        $fieldConfig = [
            'handle' => 'one_click_content_translation',
            'field' => [
                'display' => 'One-click Content Translation',
                'type' => 'one_click_content_translation_inputs',
                'listable' => 'hidden',
                'visibility' => 'hidden',
            ],
        ];

        $event->blueprint->ensureField($fieldConfig['handle'], $fieldConfig['field']);
    }
}
