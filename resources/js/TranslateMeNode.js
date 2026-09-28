import { translateMeRequest } from './api';
import { getCurrentSite } from './site';

function isTranslatable (node) {
    if (node.nodeType !== 1) return false;
    if (node.tagName === 'DIV') return false;
    if (node.textContent.trim() === '') return false;
    if (node.firstChild && node.firstChild.tagName === 'DIV') return false;
    return true;
}

export const oneClickContentTranslation = async (editor, lang = null) => {
    const dom = editor.view.dom;
    const textStrings = [];

    dom.childNodes.forEach((node, index) => {
        if (isTranslatable(node)) {
            textStrings.push({ index, html: node.innerHTML });
        }
    });

    const response = await translateMeRequest({
        target: lang || getCurrentSite(),
        texts: textStrings,
    });

    response.data.texts.forEach((text) => {
        if (dom.childNodes[text.index]) {
            dom.childNodes[text.index].innerHTML = text.html;
        }
    });
};
