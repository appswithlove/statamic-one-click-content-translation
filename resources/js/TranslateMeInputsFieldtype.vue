<template>

</template>

<script>
import { translateMeRequest } from './api';
import { isTranslationNeeded, siteFor } from './site';
import { applyBardTexts, collectBardTexts } from './TranslateMeNode';
const TRANSLATE_ALL_FIELD_TYPES = ['text-fieldtype', 'textarea-fieldtype', 'markdown-fieldtype', 'list-fieldtype'];
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
      this.renderTranslateAllButton(root, this.translationNeeded);
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
      btn.setAttribute('data-title', __('statamic-one-click-content-translation::messages.translate'));
      if (lang) {
        btn.dataset.lang = lang;
        btn.className += ' lang-detected';
        btn.setAttribute('data-title', __('statamic-one-click-content-translation::messages.translate_to', { lang: lang.toUpperCase() }));
        btn.innerHTML = lang.toUpperCase();
      }

      btn.addEventListener('click', async () => {
        const texts = this.readTexts(groupNode, node);
        if (!texts) return;

        const response = await translateMeRequest({
          target: lang || siteFor(groupNode),
          texts: texts,
        })

        this.writeTexts(groupNode, node, response.data.texts);
      })

      return btn;
    },
    readTexts(groupNode, node) {
      const isListField = groupNode.classList.contains('list-fieldtype');
      const isMarkdown = node.classList.contains('markdown-fieldtype');
      if (!isMarkdown && !isListField && node.value?.length === 0) {
        return null;
      }

      if (isListField) {
        return [...groupNode.querySelectorAll('input')].map((input, index) => ({ index, html: input.value }));
      }

      if (isMarkdown) {
        const codeMirrorNode = node.querySelector('.CodeMirror');
        if (!codeMirrorNode || codeMirrorNode.innerText === '') {
          return null;
        }
        return [{ 'index': 0, html: codeMirrorNode.CodeMirror.getValue('<br>') }];
      }

      return [{ 'index': 0, html: node.value }];
    },
    writeTexts(groupNode, node, texts) {
      if (groupNode.classList.contains('list-fieldtype')) {
        groupNode.querySelectorAll('input').forEach((input, index) => {
          input.value = texts[index].html;
        });
      } else if (node.classList.contains('markdown-fieldtype')) {
        node.querySelector('.CodeMirror').CodeMirror.setValue(texts[0].html.replaceAll('<br>', '\n'));
      } else {
        node.value = texts[0].html
      }
      const inputEvent = new Event('input', { bubbles: true, cancelable: true });
      node.dispatchEvent(inputEvent);
    },
    renderTranslateAllButton(root, show) {
      const actions = root.querySelector('header > h1 + div');
      if (!actions) return;

      const existing = actions.querySelector('.translate-me__btn--all');
      if (!show) {
        existing?.remove();
        return;
      }
      if (existing) return;

      const btn = document.createElement('button');
      btn.type = 'button';
      btn.className = 'translate-me__btn translate-me__btn--all';
      btn.textContent = __('statamic-one-click-content-translation::messages.translate_all');
      btn.addEventListener('click', async () => {
        btn.disabled = true;
        try {
          await this.translateAll(root);
        } finally {
          btn.disabled = false;
        }
      });
      actions.prepend(btn);
    },
    async translateAll(root) {
      const segments = [];
      const handledGroups = new Set();
      const inStack = el => el.closest('.stack') === root.closest('.stack');

      root.querySelectorAll(CSS_QUERY).forEach(node => {
        if (!inStack(node) || node.closest('.bard-inline-image-container')) return;

        const groupNode = node.closest('.form-group');
        if (!groupNode || handledGroups.has(groupNode)) return;
        if (!TRANSLATE_ALL_FIELD_TYPES.some(cls => groupNode.classList.contains(cls))) return;
        handledGroups.add(groupNode);

        const texts = this.readTexts(groupNode, node);
        if (texts?.length) segments.push({ texts, apply: translated => this.writeTexts(groupNode, node, translated) });
      });

      root.querySelectorAll('.ProseMirror[contenteditable="true"]').forEach(dom => {
        if (!inStack(dom)) return;

        const texts = collectBardTexts(dom);
        if (texts.length) segments.push({ texts, apply: translated => applyBardTexts(dom, translated) });
      });

      if (!segments.length) return;

      const response = await translateMeRequest({
        target: siteFor(root),
        texts: segments.flatMap(segment => segment.texts),
      });
      if (!response.status) return;

      let offset = 0;
      segments.forEach(segment => {
        const translated = response.data.texts.slice(offset, offset + segment.texts.length);
        offset += segment.texts.length;
        segment.apply(translated);
      });
    }
  }
};
</script>
