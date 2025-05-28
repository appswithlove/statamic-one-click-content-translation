<template>
  <div ref="node"></div>
</template>

<script>
import {translateMeRequest} from './api';
import LangMixin from './LangMixin';

const CSS_QUERY = 'input[type="text"]:not([readonly]), textarea:not([readonly]), .markdown-fieldtype';

export default {
  mixins: [Fieldtype, LangMixin],
  mounted() {
    document.addEventListener('DOMContentLoaded', () => {
      this.init();
      setTimeout(() => this.init(), 500);

      const parentNode = this.parentNode();
      parentNode.addEventListener('click', () => {
        setTimeout(() => this.init(), 500);
      });

      Statamic.$events.$on('publish-container-created', (event) => {
        setTimeout(() => {
          this.init(event.$el);
        }, 500);
      });
    });
  },
  watch: {
    currentLangHandle() {
      this.init();
    },
  },
  methods: {
    init(initialNode) {
      let parrentNode = this.parentNode();
      if (initialNode) {
        parrentNode = initialNode;
      }
      const card = document.querySelector('.as-sidebar .card');

      if (card && !document.querySelector('#translate-all')) {
        const btn = document.createElement('button');
        btn.className = 'flex justify-center btn w-full';
        btn.id = 'translate-all';
        btn.textContent = Statamic.$config.get('oneClickTranslation')?.translate_all;
        btn.addEventListener('click', () => {
          this.translateAll();
        });
        card.appendChild(btn);
      }

      const inputNodes = parrentNode.querySelectorAll(CSS_QUERY);

      inputNodes.forEach(node => {
        const bardImageInlineContainer = node.closest('.bard-inline-image-container');
        if (bardImageInlineContainer) {
          return;
        }

        const groupNode = node.closest('.form-group');

        const ignoreFieldTypes = ['grid-fieldtype', 'color-fieldtype'];
        if (!groupNode || ignoreFieldTypes.some((className) => groupNode.classList.contains(className))) {
          return;
        }

        const labelNode = groupNode.querySelector('label');

        if (labelNode.querySelector('.translate-me__btn')) {
          return;
        }

        labelNode.appendChild(this.createButton(groupNode, node));

        const publishField = groupNode.classList.value.match(/(publish-field__[^ ]+)/);
        const fieldName = publishField ? publishField[1] : '';
        const lang = fieldName.match(/^.*_([a-z]{2})$/);
        if (lang) {
          labelNode.appendChild(this.createButton(groupNode, node, lang[1]));
        }
      });
    },

    parentNode() {
      return this.$refs.node.closest('.publish-tab-outer');
    },

    createButton(groupNode, node, lang = null) {
      const btn = document.createElement('button');
      btn.innerHTML = '&nbsp;';
      btn.type = 'button';
      btn.className = 'ml-1 translate-me__btn';
      btn.setAttribute('data-title', this.tooltipText);

      if (lang) {
        btn.dataset.lang = lang;
        btn.className += ' lang-detected';
        btn.setAttribute('data-title', this.tooltipText + ' ' + lang.toUpperCase());
        btn.innerHTML = lang.toUpperCase();
      }

      btn.addEventListener('click', async () => {
        const isListField = groupNode.classList.contains('list-fieldtype') && groupNode?.__vue__?.$children.length;
        const isMarkdown = node.classList.contains('markdown-fieldtype');

        if (!isMarkdown && !isListField && node.value?.length === 0) {
          return;
        }

        let codeMirrorNode;
        if (isMarkdown) {
          codeMirrorNode = node.querySelector('.CodeMirror');
          if (!codeMirrorNode || codeMirrorNode.innerText === '') {
            return;
          }
        }
        let html;
        if (isListField) {
          html = groupNode?.__vue__?.value.map(value => value || ' ');
        } else if (isMarkdown) {
          html = codeMirrorNode.CodeMirror.getValue('<br>');
        } else {
          html = node.value;
        }

        const response = await translateMeRequest({
          selectedLang: lang ? lang : this.currentLang.lang,
          defaultLang: this.defaultLang.lang,
          texts: [{index: 0, html}],
        });

        const fieldData = [
          {
            isListField,
            isMarkdown,
            node,
            groupNode,
            codeMirrorNode,
            translatedHtml: response.data.texts[0].html,
          },
        ];

        this.setTranslatedData(fieldData);
      });

      return btn;
    },

    async translateAll() {
      const parentNode = this.parentNode();
      const inputNodes = parentNode.querySelectorAll(CSS_QUERY);
      const fieldsToTranslate = [];

      inputNodes.forEach((node, i) => {
        const bardImageInlineContainer = node.closest('.bard-inline-image-container');
        if (bardImageInlineContainer) {
          return;
        }

        const groupNode = node.closest('.form-group');
        const ignoreFieldTypes = ['grid-fieldtype', 'color-fieldtype'];
        if (!groupNode || ignoreFieldTypes.some(cls => groupNode.classList.contains(cls))) {
          return;
        }

        const isListField = groupNode.classList.contains('list-fieldtype') && groupNode?.__vue__?.$children.length;
        const isMarkdown = node.classList.contains('markdown-fieldtype');

        if (!isMarkdown && !isListField && node.value?.length === 0) {
          return;
        }

        let codeMirrorNode;
        if (isMarkdown) {
          codeMirrorNode = node.querySelector('.CodeMirror');
          if (!codeMirrorNode || codeMirrorNode.innerText === '') {
            return;
          }
        }

        let html;
        if (isListField) {
          html = groupNode?.__vue__?.value.map(value => value || ' ');
        } else if (isMarkdown) {
          html = codeMirrorNode.CodeMirror.getValue('<br>');
        } else {
          html = node.value;
        }

        fieldsToTranslate.push({
          id: 'field_' + i,
          isListField,
          isMarkdown,
          node,
          groupNode,
          codeMirrorNode,
          html,
        });
      });

      const texts = fieldsToTranslate.map((field) => ({
        id: field.id,
        html: field.html,
      }));

      const response = await translateMeRequest({
        selectedLang: this.currentLang.lang,
        defaultLang: this.defaultLang.lang,
        texts,
      });

      const fieldsData = response.data.texts.map((item) => {
        const match = fieldsToTranslate.find((f) => f.id === item.id);
        return {
          ...match,
          translatedHtml: item.html,
        };
      });

      this.setTranslatedData(fieldsData);

      const bardFields = document.querySelectorAll('.bard-fieldtype');

      bardFields.forEach((bardField) => {
        const vueComponent = bardField.__vue__;
        const editor = vueComponent?.$refs?.field?.editor;

        if (editor?.commands?.oneClickContentTranslation) {
          editor.commands.oneClickContentTranslation({
            selectedLang: this.currentLang.lang,
            defaultLang: this.defaultLang.lang,
          });
        }
      });
    },

    async setTranslatedData(fieldsData) {
      for (const field of fieldsData) {
        const {isListField, isMarkdown, node, groupNode, codeMirrorNode, translatedHtml} = field;
        if (isListField) {
          if (typeof groupNode.__vue__.$children[0].update === 'function') {
            groupNode.__vue__.$children[0].update(translatedHtml);
          } else {
            groupNode.__vue__.value = translatedHtml;
          }
        } else if (isMarkdown) {
          codeMirrorNode.CodeMirror.setValue(translatedHtml.replaceAll('<br>', '\n'));
        } else {
          node.value = node.id === 'field_slug' ? this.slugify(translatedHtml) : translatedHtml;
        }

        const inputEvent = new Event('input', {bubbles: true, cancelable: true});
        node.dispatchEvent(inputEvent);

        await this.sleep(100);
      }
    },

    sleep(ms) {
      return new Promise(resolve => setTimeout(resolve, ms));
    },
    slugify(text) {
      return text.toString().toLowerCase().trim().replace(/\s+/g, '-').replace(/[^\w\-]+/g, '');
    },
  },
};
</script>
