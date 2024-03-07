<template>
  <div ref="node"></div>
</template>

<script>
import { translateMeRequest } from './api';
import LangMixin from './LangMixin';

const CSS_QUERY = 'input[type="text"]:not([readonly]), textarea:not([readonly]), .markdown-fieldtype';

export default {
  mixins: [Fieldtype, LangMixin],
  mounted(){
    this.init();
    const parentNode = this.parentNode();
    parentNode.addEventListener('click', () => {
      setTimeout(this.init(), 500);
    })

    Statamic.$events.$on('publish-container-created', (event) => { setTimeout( () => {
        this.init(event.$el);
    }, 500) });
  },
  watch: {
    currentLangHandle() {
      this.init();
    }
  },
  methods: {
    init(initialNode) {
      if (this.currentLang.lang === this.defaultLang.lang) {
        return;
      }

      let parrentNode = this.parentNode();
      if (initialNode) {
        parrentNode = initialNode;
      }

      const inputNodes = parrentNode.querySelectorAll(CSS_QUERY);

      inputNodes.forEach(node => {
        const groupNode = node.closest('.form-group');

        const ignoreFieldTypes = ['grid-fieldtype'];
        if (!groupNode || ignoreFieldTypes.some(className => groupNode.classList.contains(className))) {
          return;
        }

        const labelNode = groupNode.querySelector('label');

        if (labelNode.querySelector('.translate-me__btn')) {
          return;
        }

        labelNode.appendChild(this.createButton(groupNode, node));

        const fieldName = groupNode.classList.value.match(/(publish-field__[^ ]+)/)[1];
        const lang = fieldName.match(/^.*_([a-z]{2})$/);
        if (lang) {
          labelNode.appendChild(this.createButton(groupNode, node, lang[1]));
        }
      })
    },
    parentNode () {
      return this.$refs.node.closest('.publish-tab-outer');
    },
    createButton (groupNode, node, lang = null) {
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
        
        btn.addEventListener('click', async (event) => {
          let texts = [];

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

          if (isListField) {
            texts = groupNode?.__vue__?.value.map((value, index) => ({ index, html: value }));
          } else if (isMarkdown) {
            texts = [{ 'index': 0, html: codeMirrorNode.CodeMirror.getValue('<br>') }];
          } else {
            texts = [{ 'index': 0, html: node.value }];
          }

          const response = await translateMeRequest({
            selectedLang: lang ? lang : this.currentLang.lang,
            defaultLang: this.defaultLang.lang,
            texts: texts,
          })

          if (isListField) {
            groupNode.__vue__.$children[0].update(response.data.texts.map((item) => item.html))
          } else if (isMarkdown) {
            codeMirrorNode.CodeMirror.setValue(response.data.texts[0].html.replaceAll('<br>', '\n'));
          } else {
            node.value = response.data.texts[0].html
          }
          const inputEvent = new Event('input', { bubbles:true, cancelable: true });
          node.dispatchEvent(inputEvent);
        })

        return btn;
    }
  }
};
</script>
