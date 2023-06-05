<template>
  <div ref="node"></div>
</template>

<script>
import { translateMeRequest } from './api';
import LangMixin from './LangMixin';

const CSS_QUERY = 'input[type="text"]:not([readonly]), textarea:not([readonly])';

export default {
  mixins: [Fieldtype, LangMixin],
  mounted(){
    this.init();
    const parentNode = this.parentNode();
    parentNode.addEventListener('click', () => {
      setTimeout(this.init(), 500);
    })
  },
  watch: {
    currentLangHandle() {
      this.init();
    }
  },
  methods: {
    init() {
      if (this.currentLang.lang === this.defaultLang.lang) {
        return;
      }

      const parrentNode = this.parentNode();
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

        const btn = document.createElement('button');
        btn.innerHTML = '&nbsp;';
        btn.type = 'button';
        btn.className = 'ml-1 translate-me__btn';
        btn.setAttribute('data-title', this.tooltipText);

        labelNode.appendChild(btn);
        btn.addEventListener('click', async (event) => {
          let texts = [];
          const isListField = groupNode.classList.contains('list-fieldtype') && groupNode?.__vue__?.$children.length;

          if (!isListField && node.value.length === 0) {
            return;
          }

          if (isListField) {
            texts = groupNode?.__vue__?.value.map((value, index) => ({ index, html: value }));
          } else {
            texts = [{ 'index': 0, html: node.value }];
          }

          const response = await translateMeRequest({
            selectedLang: this.currentLang.lang,
            defaultLang: this.defaultLang.lang,
            texts: texts,
          })

          if (isListField) {
            groupNode.__vue__.$children[0].update(response.data.texts.map((item) => item.html))
          } else {
            node.value = response.data.texts[0].html
          }
          const inputEvent = new Event('input', { bubbles:true, cancelable: true });
          node.dispatchEvent(inputEvent);
        })
      })
    },
    parentNode () {
      return this.$refs.node.closest('.publish-tab-outer');
    }
  }
};
</script>
