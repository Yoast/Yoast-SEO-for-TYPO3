import store from "@yoast/yoast-seo-for-typo3/store.js";
class SnippetPreview {
    constructor() {
        this.previewElement = null;
        this.contentState = null;
        store.subscribe((state) => {
            if (!state.content ||
                JSON.stringify(state.content) === JSON.stringify(this.contentState)) {
                return;
            }
            this.contentState = state.content;
            this.updatePreview(state);
        });
    }
    getElement() {
        if (!this.previewElement) {
            this.previewElement = document.querySelector("yoast-snippet-preview");
        }
        return this.previewElement;
    }
    updatePreview(state) {
        const previewElement = this.getElement();
        if (!state.content || !previewElement)
            return;
        previewElement.setAttribute("site-name", state.siteName);
        previewElement.setAttribute("locale", state.content.locale);
        previewElement.setAttribute("url", state.content.url);
        previewElement.setAttribute("title", state.content.title);
        previewElement.setAttribute("description", state.content.metadata.description || "");
        previewElement.setAttribute("favicon-src", state.content.favicon);
        if (state.focusKeyphrase) {
            previewElement.setAttribute("words-to-highlight", JSON.stringify([state.focusKeyphrase.keyword]));
        }
    }
}
export default new SnippetPreview();
