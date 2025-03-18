import {html, LitElement} from 'lit';
import {customElement, state} from 'lit/decorators.js';
import "@yoast/yoast-seo-for-typo3/loading-spinner.js";
import {store} from "@yoast/yoast-seo-for-typo3/store.js";

@customElement('yoast-snippet-preview')
export class SnippetPreview extends LitElement {
  @state() private _title: string = '';
  @state() private _description: string = '';
  @state() private _url: string = '';
  @state() private _faviconSrc: string = '';
  @state() private _locale: string = '';
  private readonly unsubscribe: () => void;

  constructor() {
    super();
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

  createRenderRoot(): HTMLElement | DocumentFragment {
    return this;
  }

  render() {
    if (this._title && this._url) {
      return html`
        <yoast-lib-snippet-preview title="${this._title}" description="${this._description}" url="${this._url}"
                                   favicon-src="${this._faviconSrc}"
                                   locale="${this._locale}"></yoast-lib-snippet-preview>`;
    }
    return html`
      <yoast-loading-spinner></yoast-loading-spinner>`;
  }
}

declare global {
  interface HTMLElementTagNameMap {
    'yoast-snippet-preview': SnippetPreview;
  }
}
