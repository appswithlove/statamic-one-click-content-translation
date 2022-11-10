<template>
    <div class="class-type-wrapper" v-if="enabled">
        <button
            class="bard-toolbar-button translate-me__btn translate-me__btn--bard"
            v-html="'&nbsp;'"
            v-tooltip="tooltipText"
            @click="translateMe"
            :disabled="disabled"
        ></button>
    </div>
  </template>
  <script>
  import LangMixin from './LangMixin';
  
  export default {
    mixins: [BardToolbarButton, LangMixin],
    methods: {
        translateMe() {
            this.editor.commands.translateMe({
                selectedLang: this.currentLang.lang,
                defaultLang: this.defaultLang.lang
            })
        },
    }
  };
  </script>
  