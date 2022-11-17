<?php

namespace Appswithlove\StatamicOneClickContentTranslation;

use Statamic\Events\EntryBlueprintFound;
use Statamic\Facades\User;

class BlueprintListener
{
    public function handle(EntryBlueprintFound $event)
    {
        if (!User::current()) {
            return;
        }

        $contents = $event->blueprint->contents();

        $firstKey = array_key_first($contents['sections']);

        $contents['sections'][$firstKey]['fields'][] = [
            'handle' => 'one_click_content_translation',
            'field' => [
                'display' => 'One-click Content Translation',
                 'type' => 'one_click_content_translation_inputs',
                'listable' => 'hidden',
                'visibility' => 'hidden'
            ],
        ];

        $event->blueprint->setContents($contents);
    }
}
