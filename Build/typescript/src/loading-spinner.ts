import {html, LitElement} from 'lit';
import {customElement} from 'lit/decorators.js';

@customElement('yoast-loading-spinner')
export class LoadingSpinner extends LitElement {
  createRenderRoot(): HTMLElement | DocumentFragment {
    return this;
  }

  render() {
    return html`
      <div class="spinner">
        <div class="bounce bounce1"></div>
        <div class="bounce bounce2"></div>
        <div class="bounce bounce3"></div>
      </div>
    `;
  }
}

declare global {
  interface HTMLElementTagNameMap {
    'yoast-loading-spinner': LoadingSpinner;
  }
}
