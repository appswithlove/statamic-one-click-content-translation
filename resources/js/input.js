import TranslateMeInputsFieldtype from './TranslateMeInputsFieldtype.vue';

Statamic.$components.register('one_click_content_translation_inputs-fieldtype', TranslateMeInputsFieldtype);

// @TODO replace TranslateMeInputsFieldtype to js file with event instead of the vue component
// Statamic.$events.$on('publish-container-created', (event) => { setTimeout( () => {
//     console.log('Ola', event, Statamic); 
// }, 500) });