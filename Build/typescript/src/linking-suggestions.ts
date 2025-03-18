import AjaxRequest from "@typo3/core/ajax/ajax-request.js"
import analysis from "@yoast/yoast-seo-for-typo3/analysis.js"
import { YoastLinkingSuggestions } from "@yoast/yoast-seo-for-typo3/types/yoast"
import worker from "@yoast/yoast-seo-for-typo3/web-worker.js"
import YoastConfiguration from "@yoast/yoast-seo-for-typo3/yoast-configuration.js"

import "@yoast/yoast-seo-for-typo3/dist/webcomponents.js"

import { AjaxResponse } from "@yoast/yoast-seo-for-typo3/helpers/ajax-response.js"
import { setAttributes } from "@yoast/yoast-seo-for-typo3/helpers/attributes.js"

type LinkingSuggestionsConfig = {
  urls: {
    workerUrl: string
    linkingSuggestions: string
  }
  locale: string
  languageId: number
  currentPage: number
  supportedLanguages: string[]
}

type CkeditorElement = HTMLElement & {
  ckeditorInstance?: {
    getData: () => string
    sourceElement?: HTMLTextAreaElement
  }
}

class LinkingSuggestions {
  private configuration: LinkingSuggestionsConfig | null = null
  private updateInterval: number | null = null
  public initialize(configuration: LinkingSuggestionsConfig): void {
    YoastConfiguration.setUrl("workerUrl", configuration.urls.workerUrl)
    YoastConfiguration.setSupportedLanguages(configuration.supportedLanguages)

    this.configuration = configuration

    worker.set(false, this.configuration.locale)

    this.updateInterval = setInterval(() => {
      this.checkSuggestions()
    }, 1000)
  }

  private checkSuggestions(): void {
    const content = this.getCkeditorContent()
    if (content === null) {
      return
    }

    this.getLinkingSuggestions(content)

    // Analysis is done, Updating every second is not necessary anymore
    clearInterval(this.updateInterval!)
    this.updateInterval = setInterval(() => {
      this.checkSuggestions()
    }, 10000)
  }

  private async getLinkingSuggestions(ckeditorContent: string): Promise<void> {
    const suggestionsElement = document.querySelector(
      "yoast-linking-suggestions"
    ) as HTMLElement | null
    if (!suggestionsElement) return

    const content = analysis.createContent({
      body: ckeditorContent,
      locale: this.configuration!.locale,
    })
    const response = await analysis.runResearch<YoastLinkingSuggestions>(
      "getProminentWordsForInternalLinking",
      content
    )
    new AjaxRequest(this.configuration!.urls.linkingSuggestions)
      .post(
        {
          excludedPage: this.configuration!.currentPage,
          languageId: this.configuration!.languageId,
          content: ckeditorContent,
          words: response.result.prominentWords.slice(0, 5),
        },
        { headers: { "Content-Type": "application/json" } }
      )
      .then(async (response: AjaxResponse) => {
        const result = await response.resolve<{
          links: YoastLinkingSuggestions[]
        }>()
        setAttributes(suggestionsElement, {
          "is-checking": "false",
          links: JSON.stringify(result.links),
        })
      })
  }

  private getCkeditorContent(): string | null {
    const ckeditor5elements = document.getElementsByTagName(
      "typo3-rte-ckeditor-ckeditor5"
    )
    if (ckeditor5elements.length > 0) {
      const editableElements = document.querySelectorAll(
        ".ck-editor__editable"
      ) as NodeListOf<CkeditorElement>
      let content = ""
      let ckeditorLoaded = false
      for (let editorElement in editableElements) {
        if (
          typeof editableElements[editorElement].ckeditorInstance !==
          "undefined"
        ) {
          ckeditorLoaded = true
          content += editableElements[editorElement].ckeditorInstance.getData()
        }
      }
      if (ckeditorLoaded === false) {
        return null
      }
      return content
    }
    return null
  }
}

export default new LinkingSuggestions()
