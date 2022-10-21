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

      const parrentNode = this.$refs.node.closest('.publish-section-wrapper').parentElement;
      const inputNodes = parrentNode.querySelectorAll(CSS_QUERY);
      inputNodes.forEach(node => {
        const groupNode = node.closest('.form-group');
        const labelNode = groupNode.querySelector('label');

        if (labelNode.querySelector('.translate-me__btn')) {
          return;
        }

        const btn = document.createElement('button');
        btn.innerHTML = '<i class="fa fa-language"></i>';
        btn.type = 'button';
        btn.className = 'btn btn-sm ml-1 translate-me__btn';
        btn.setAttribute('title', this.tooltipText)

        labelNode.appendChild(btn);
        btn.addEventListener('click', async (event) => {
          const response = await translateMeRequest({
            selectedLang: this.currentLang.lang,
            defaultLang: this.defaultLang.lang,
            texts: [{ 'index': 0, html: node.value }],
          })
          node.value = response.data.texts[0].html
        })
      })
    }
  }
};
</script>
