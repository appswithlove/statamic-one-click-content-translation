import { translateMeRequest } from './api';

const { Node } = Statamic.$bard.tiptap.core;
export default class TranslateMeNode extends Node {


  name() {
    return 'translateMe'
  }

  schema() {
    return {
    }
  }

  commands({ type }) {
    return attrs => async (state, dispatch, event) => {

      const textStrings = [];
      event.dom.childNodes.forEach((node, index) => {
        if(this.isTranslatable(node)) {
          textStrings.push({ index, html: node.innerHTML });
        }
      })

      const response = await translateMeRequest({
        selectedLang: attrs.selectedLang,
        defaultLang: attrs.defaultLang,
        texts: textStrings
      })

      response.data.texts.forEach((text) => {
        event.dom.childNodes[text.index].innerHTML = text.html
      })

      Statamic.$toast.success(__('Done'));
    }
}

  plugins() {
      return [];
  }

  isTranslatable(node) {
    if(node.tagName === 'DIV') {
      return false;
    }

    if(node.textContent.trim() === '') {
      return false;
    }

    return true;
  }

}
