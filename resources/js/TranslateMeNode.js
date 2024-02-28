import { translateMeRequest } from './api';

const { Node } = Statamic.$bard.tiptap.core;
function isTranslatable(node) {
    if(node.tagName === 'DIV') {
      return false;
    }

    if(node.textContent.trim() === '') {
      return false;
    }

    if(node.firstChild && node.firstChild.tagName === 'DIV') {
        return false;
    }

    return true;
}

export const TranslateMeNode = Node.create({
    name: 'oneClickContentTranslation',

    addCommands() {
        return {
            oneClickContentTranslation: (attrs) => async (event) => {
                const dom = event.editor.view.dom;

                const textStrings = [];
                dom.childNodes.forEach((node, index) => {
                    if(isTranslatable(node)) {
                        textStrings.push({ index, html: node.innerHTML });
                    }
                })

                const response = await translateMeRequest({
                    selectedLang: attrs.selectedLang,
                    defaultLang: attrs.defaultLang,
                    texts: textStrings
                })

                response.data.texts.forEach((text) => {
                    dom.childNodes[text.index].innerHTML = text.html;
                })
            }
        };

    },

})
