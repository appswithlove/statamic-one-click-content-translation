<template>
    <div class="class-type-wrapper translate-me__d-flex" v-if="enabled">
        <button
            class="bard-toolbar-button translate-me__btn translate-me__btn--bard"
            v-html="'&nbsp;'"
            v-tooltip="tooltipText"
            @click="translateMe"
            :disabled="disabled"
        ></button>
        <button
            v-if="langDetected"
            class="bard-toolbar-button translate-me__btn translate-me__btn--bard lang-detected"
            v-html="langDetected.toUpperCase()"
            v-tooltip="`${tooltipText} ${langDetected.toUpperCase()}`"
            @click="translateDetectedLang"
            :disabled="disabled"
            :data-lang="langDetected"
        ></button>
    </div>
  </template>
  <script>
  import LangMixin from './LangMixin';
  
  export default {
    mixins: [BardToolbarButton, LangMixin],
    computed: {
        fieldName() {
            return this.$options.propsData?.config?.handle || '';
        },
        langDetected() {
            const lang = this.fieldName.match(/^.*_([a-z]{2})$/);

            return lang ? lang[1] : null;
        },
    },
    methods: {
        translateMe() {
            this.editor.commands.oneClickContentTranslation({
                selectedLang: this.currentLang.lang,
                defaultLang: this.defaultLang.lang
            })
        },
        translateDetectedLang() {
            this.editor.commands.oneClickContentTranslation({
                selectedLang: this.langDetected || this.currentLang.lang,
                defaultLang: this.defaultLang.lang
            })
        }
    },

  };
  </script>