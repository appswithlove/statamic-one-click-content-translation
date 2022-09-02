<?php

namespace Appswithlove\StatamicTranslateMe;

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
            'handle' => 'translate_me',
            'field' => [
                'display' => 'Translate Me',
                 'type' => 'translate_me_inputs',
                'listable' => 'hidden',
                'visibility' => 'hidden'
            ],
        ];

        $event->blueprint->setContents($contents);
    }
}
