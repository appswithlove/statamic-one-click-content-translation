<template>

</template>

<script>
import { translateMeRequest } from './api';
import { isTranslationNeeded, siteFor } from './site';
const CSS_QUERY = 'input[type="text"]:not([readonly]), textarea:not([readonly]), .markdown-fieldtype, .list-fieldtype';

export default {
  setup() {
    const context = window.__STATAMIC__.ui.injectPublishContext();
    return { publishSite: context?.site };
  },
  watch: {
    publishSite() {
      this.markSite();
      this.scheduleInit();
    },
  },
  mounted() {
    this.markSite();
    this.scheduleInit();

    const initializedEditors = new WeakSet();
    this.observer = new MutationObserver(() => {
      document.querySelectorAll('.asset-editor').forEach(assetEditor => {
        if (!initializedEditors.has(assetEditor)) {
          initializedEditors.add(assetEditor);
          setTimeout(() => this.init(assetEditor, this.translationNeeded), 2000);
        }
      });

      clearTimeout(this.refreshTimer);
      this.refreshTimer = setTimeout(() => this.initOwnForm(), 300);
    });
    this.observer.observe(document.body, { childList: true, subtree: true });
  },
  beforeUnmount() {
    clearTimeout(this.initTimer);
    clearTimeout(this.refreshTimer);
    if (this.observer) this.observer.disconnect();
  },
  methods: {
    markSite() {
      const fieldNode = this.$el.parentElement;
      if (!fieldNode) return;

      if (this.publishSite) fieldNode.dataset.oneClickSite = this.publishSite;
      else delete fieldNode.dataset.oneClickSite;
    },
    scheduleInit() {
      clearTimeout(this.initTimer);
      this.initTimer = setTimeout(() => this.initOwnForm(), 2000);
    },
    initOwnForm() {
      const root = this.$el.parentElement?.closest('.stack') ?? document.querySelector('#main');
      if (!root) return;

      this.translationNeeded = isTranslationNeeded(siteFor(root));
      this.init(root, this.translationNeeded);
    },
    init(el, showDefaultButton = true) {
      const inputNodes = el.querySelectorAll(CSS_QUERY);
      inputNodes.forEach(node => {
        if (node.closest('.stack') !== el.closest('.stack')) return;

        const bardImageInlineContainer = node.closest('.bard-inline-image-container');
        if (bardImageInlineContainer) return;

        const groupNode = node.closest('.form-group');
        const ignoreFieldTypes = ['grid-fieldtype', 'color-fieldtype'];
        if (!groupNode || ignoreFieldTypes.some(cls => groupNode.classList.contains(cls))) return;

        const labelNode = groupNode.querySelector('label');
        if (!labelNode) return;

        const fieldHandle = (node.id || labelNode.getAttribute('for') || '').replace(/^field_/, '');
        const lang = fieldHandle.match(/^.*_([a-z]{2})$/);

        if (lang && !labelNode.querySelector(`.translate-me__btn[data-lang="${lang[1]}"]`)) {
          labelNode.appendChild(this.createButton(groupNode, node, lang[1]));
        }

        const defaultButton = labelNode.querySelector('.translate-me__btn:not([data-lang])');
        if (showDefaultButton && !defaultButton) {
          labelNode.appendChild(this.createButton(groupNode, node));
        } else if (!showDefaultButton && defaultButton) {
          defaultButton.remove();
        }
      })
    },
    createButton (groupNode, node, lang = null) {
      const btn = document.createElement('button');
      btn.innerHTML = '&nbsp;';
      btn.type = 'button';
      btn.className = 'ml-1 translate-me__btn';
      btn.setAttribute('data-title', 'Translate');
      if (lang) {
        btn.dataset.lang = lang;
        btn.className += ' lang-detected';
        btn.setAttribute('data-title', 'Translate ' + lang.toUpperCase());
        btn.innerHTML = lang.toUpperCase();
      }

      btn.addEventListener('click', async (event) => {
        let texts = [];
        const isListField = groupNode.classList.contains('list-fieldtype');
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
          groupNode.querySelectorAll('input').forEach((input, index) => {
            texts.push({ index, html: input.value });
          });
        } else if (isMarkdown) {
          texts = [{ 'index': 0, html: codeMirrorNode.CodeMirror.getValue('<br>') }];
        } else {
          texts = [{ 'index': 0, html: node.value }];
        }
        const response = await translateMeRequest({
          target: lang || siteFor(groupNode),
          texts: texts,
        })

        if (isListField) {
          groupNode.querySelectorAll('input').forEach((input, index) => {
            input.value = response.data.texts[index].html;
          });
        } else if (isMarkdown) {
          codeMirrorNode.CodeMirror.setValue(response.data.texts[0].html.replaceAll('<br>', '\n'));
        } else {
          node.value = response.data.texts[0].html
        }
        const inputEvent = new Event('input', { bubbles: true, cancelable: true });
        node.dispatchEvent(inputEvent);
      })

      return btn;
    }
  }
};
</script>
