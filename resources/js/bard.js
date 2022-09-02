import TranslateMeNode from './TranslateMeNode';
import TranslateMeMenu from "./TranslateMeMenu.vue";

Statamic.$bard.extend(({ node }) => node(new TranslateMeNode()));

Statamic.$bard.buttons(() => {
  return {
      name: 'translate-me',
      text: 'Translate',
      command: 'classType',
      icon: 'language',
      component: TranslateMeMenu
  };
});
