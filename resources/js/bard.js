import TranslateMeNode from './TranslateMeNode';
import TranslateMeMenu from "./TranslateMeMenu.vue";

Statamic.$bard.extend(({ node }) => node(new TranslateMeNode()));

Statamic.$bard.buttons(() => {
  return {
      name: 'one-click-content-translation',
      text: 'Translate',
      command: 'classType',
      component: TranslateMeMenu
  };
});
