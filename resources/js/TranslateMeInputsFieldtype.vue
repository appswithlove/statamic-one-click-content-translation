<template>

</template>

<script>
import { isTranslateNeedRequest, translateMeRequest } from './api';
const CSS_QUERY = 'input[type="text"]:not([readonly]), textarea:not([readonly]), .markdown-fieldtype, .list-fieldtype';

export default {
  async mounted() {
    const self = this;
    let translationNeeded = false;

    async function checkAndInit() {
      const response = await isTranslateNeedRequest({
        url: window.location.pathname,
      });

      translationNeeded = response === true;

      const el = document.querySelector('#main');
      setTimeout(() => {
        if (el) self.init(el, translationNeeded);
      }, 2000);
    }

    await checkAndInit();

    const initializedEditors = new WeakSet();
    const observer = new MutationObserver(() => {
      document.querySelectorAll('.asset-editor').forEach(assetEditor => {
        if (!initializedEditors.has(assetEditor)) {
          initializedEditors.add(assetEditor);
          setTimeout(() => self.init(assetEditor, translationNeeded), 2000);
        }
      });
    });
    observer.observe(document.body, { childList: true, subtree: true });

    (function() {
      const pushState = history.pushState;
      history.pushState = function(...args) {
        pushState.apply(history, args);
        window.dispatchEvent(new Event('urlchange'));
      };

      const replaceState = history.replaceState;
      history.replaceState = function(...args) {
        replaceState.apply(history, args);
        window.dispatchEvent(new Event('urlchange'));
      };
    })();

    window.addEventListener('urlchange', () => {
      checkAndInit();
    });
  },
  methods: {
    init(el, showDefaultButton = true) {
      const inputNodes = el.querySelectorAll(CSS_QUERY);
      inputNodes.forEach(node => {
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

        if (showDefaultButton && !labelNode.querySelector('.translate-me__btn:not([data-lang])')) {
          labelNode.appendChild(this.createButton(groupNode, node));
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
          url: window.location.pathname,
          texts: texts,
          ...(lang ? { lang } : {}),
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
