import { setAttributes } from "@yoast/yoast-seo-for-typo3/helpers/attributes.js"
import store from "@yoast/yoast-seo-for-typo3/store.js"
import {
  RequestError,
  State,
  YoastContent,
} from "@yoast/yoast-seo-for-typo3/types/yoast"

export default class SnippetPreview {
  private previewElement: HTMLElement | null = null
  private contentState: YoastContent | null = null

  init(): void {
    store.subscribe((state) => {
      if (typeof state.error !== "undefined" && state.error !== null) {
        this.setAttributesToErrorState(state.error)
        return
      }
      if (
        !state.content ||
        JSON.stringify(state.content) === JSON.stringify(this.contentState)
      ) {
        return
      }
      this.contentState = state.content
      this.updatePreview(state)
    })
  }

  private getElement(): HTMLElement | null {
    if (!this.previewElement) {
      this.previewElement = document.querySelector("yoast-snippet-preview")
    }
    return this.previewElement
  }

  updatePreview(state: State): void {
    const previewElement = this.getElement()
    if (!state.content || !previewElement) return

    setAttributes(previewElement, {
      "site-name": state.siteName,
      locale: state.content.locale,
      url: state.content.url,
      title: state.content.title,
      description: state.content.metadata.description || "...",
      "favicon-src": state.content.favicon,
    })

    if (state.focusKeyphrase) {
      previewElement.setAttribute(
        "words-to-highlight",
        JSON.stringify([state.focusKeyphrase.keyword])
      )
    }
  }

  setAttributesToErrorState(error: RequestError["error"]): void {
    const previewElement = this.getElement()
    if (!previewElement) return

    setAttributes(previewElement, {
      error: JSON.stringify(error),
    })
  }
}
