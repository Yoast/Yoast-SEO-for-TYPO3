var __decorate = (this && this.__decorate) || function (decorators, target, key, desc) {
    var c = arguments.length, r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc, d;
    if (typeof Reflect === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);
    else for (var i = decorators.length - 1; i >= 0; i--) if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
    return c > 3 && r && Object.defineProperty(target, key, r), r;
};
import { html, LitElement } from 'lit';
import { customElement, state } from 'lit/decorators.js';
import "@yoast/yoast-seo-for-typo3/loading-spinner.js";
import { store } from "@yoast/yoast-seo-for-typo3/store.js";
let SnippetPreview = class SnippetPreview extends LitElement {
    constructor() {
        super();
        this._title = '';
        this._description = '';
        this._url = '';
        this._faviconSrc = '';
        this._locale = '';
        this.unsubscribe = store.subscribe((state) => {
            this._title = state.title || '';
            this._description = state.description || '';
            this._url = state.url || '';
            this._faviconSrc = state.faviconSrc || '';
            this._locale = state.locale || '';
        });
    }
    disconnectedCallback() {
        super.disconnectedCallback();
        this.unsubscribe();
    }
    createRenderRoot() {
        return this;
    }
    render() {
        if (this._title && this._url) {
            return html `
        <yoast-lib-snippet-preview title="${this._title}" description="${this._description}" url="${this._url}"
                                   favicon-src="${this._faviconSrc}"
                                   locale="${this._locale}"></yoast-lib-snippet-preview>`;
        }
        return html `
      <yoast-loading-spinner></yoast-loading-spinner>`;
    }
};
__decorate([
    state()
], SnippetPreview.prototype, "_title", void 0);
__decorate([
    state()
], SnippetPreview.prototype, "_description", void 0);
__decorate([
    state()
], SnippetPreview.prototype, "_url", void 0);
__decorate([
    state()
], SnippetPreview.prototype, "_faviconSrc", void 0);
__decorate([
    state()
], SnippetPreview.prototype, "_locale", void 0);
SnippetPreview = __decorate([
    customElement('yoast-snippet-preview')
], SnippetPreview);
export { SnippetPreview };
