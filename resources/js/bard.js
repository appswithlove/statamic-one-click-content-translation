import { TranslateMeNode } from './TranslateMeNode';
import TranslateMeMenu from "./TranslateMeMenu.vue";

Statamic.$bard.addExtension(() => TranslateMeNode);

Statamic.$bard.buttons((buttons, button) => {
  return {
      name: 'oneClickContentTranslation',
      text: 'Translate',
      component: TranslateMeMenu
  };
});
