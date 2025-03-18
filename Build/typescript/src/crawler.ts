import FrontendRequest from "@maxserv/frontend-request/frontend-request.js"
import AjaxRequest from "@typo3/core/ajax/ajax-request.js"
import analysis from "@yoast/yoast-seo-for-typo3/analysis.js"
import { AjaxResponse } from "@yoast/yoast-seo-for-typo3/helpers/ajax-response.js"
import { saveProminentWords } from "@yoast/yoast-seo-for-typo3/helpers/prominent-words.js"
import {
  YoastContent,
  YoastLinkingSuggestions,
} from "@yoast/yoast-seo-for-typo3/types/yoast"
import worker from "@yoast/yoast-seo-for-typo3/web-worker.js"
import YoastConfiguration from "@yoast/yoast-seo-for-typo3/yoast-configuration.js"

type CrawlerConfig = {
  urls: {
    workerUrl: string
    determinePages: string
    indexPages: string
    prominentWords: string
  }
  supportedLanguages: string[]
}

class Crawler {
  private configuration: CrawlerConfig | null = null
  private progressCache: Record<string, number> = {}

  public initialize(configuration: CrawlerConfig): void {
    this.configuration = configuration

    YoastConfiguration.setUrl("workerUrl", configuration.urls.workerUrl)
    YoastConfiguration.setSupportedLanguages(configuration.supportedLanguages)

    this.attachIndexButtonHandlers()
  }

  private attachIndexButtonHandlers() {
    const indexButtons = document.querySelectorAll(
      ".js-crawler-index"
    ) as NodeListOf<HTMLButtonElement>
    indexButtons.forEach((button) => {
      button.addEventListener("click", () => {
        const site = button.dataset.site || ""
        const language = button.dataset.language || ""
        this.handleStartIndex(site, language)
      })
    })
  }
  private handleStartIndex(site: string, language: string) {
    this.toggleButtons(true)
    this.removeSavedProgress(site, language)

    const progressDiv = this.getProgressContainer(site, language)
    if (!progressDiv) return

    const progressInfo = progressDiv.querySelector(
      ".js-crawler-pages-progress"
    ) as HTMLElement | null
    const progressSuccess = progressDiv.querySelector(
      ".js-crawler-pages-success"
    ) as HTMLElement | null
    if (!progressInfo || !progressSuccess) return

    progressDiv.classList.remove("hide")

    new AjaxRequest(this.configuration!.urls.determinePages)
      .post(
        { site, language },
        { headers: { "Content-Type": "application/json" } }
      )
      .then(async (response: AjaxResponse) => {
        const result = await response.resolve<{
          error?: string
          amount?: number
        }>()
        if (result.error) {
          this.showError(progressInfo, result.error)
        } else {
          this.showPageAmount(progressInfo, progressSuccess, result.amount)
          this.runIndex(site, language)
        }
      })
  }

  private runIndex(site: string, language: string) {
    const progressDiv = this.getProgressContainer(site, language)
    const progressBar = progressDiv?.querySelector(
      ".js-crawler-progress-bar"
    ) as HTMLDivElement | null
    if (!progressBar) return

    const offsetKey = `${site}-${language}`
    const offset = this.progressCache[offsetKey] ?? 0

    this.postRequest(this.configuration!.urls.indexPages, {
      site,
      language,
      offset,
    }).then(async (response: AjaxResponse) => {
      const result = await response.resolve<{
        status?: boolean
        total?: number
        nextOffset?: string
      }>()

      if (result.status) {
        this.showSuccess(progressBar, result.total)
        this.toggleButtons(false)
      } else {
        this.progressCache[offsetKey] = parseInt(result.nextOffset, 10)
        await this.process(language, result, progressBar)
        this.runIndex(site, language)
      }
    })
  }

  private async process(
    language: string,
    result: any,
    progressBar: HTMLDivElement
  ): Promise<void> {
    let current = result.current
    let total = result.total
    let percentage = Math.round((current / total) * 100)

    this.updateProgressBar(progressBar, current, total, percentage)
    for (let page in result.pages) {
      if (result.pages.hasOwnProperty(page)) {
        let pageId = result.pages[page]
        const response: AjaxResponse = await FrontendRequest.request({
          pageId: pageId,
          languageId: language,
        })
        const frontendResult = await response.resolve<YoastContent>()
        await this.processRelevantWords(pageId, language, frontendResult)
        current++
        percentage = Math.round((current / total) * 100)
        this.updateProgressBar(progressBar, current, total, percentage)
      }
    }
  }

  private async processRelevantWords(
    pageId: string,
    language: string,
    result: YoastContent
  ): Promise<void> {
    worker.set(false, result.locale)
    const content = analysis.createContent({
      url: result.url,
      title: result.title,
      body: result.body,
      locale: result.locale,
    })
    const response = await analysis.runResearch<YoastLinkingSuggestions>(
      "getProminentWordsForInternalLinking",
      content
    )
    saveProminentWords(
      this.configuration!.urls.prominentWords,
      "pages",
      pageId,
      language,
      response.result
    )
  }

  private updateProgressBar(
    progressBar: HTMLDivElement,
    current: number,
    total: number,
    percentage: number
  ): void {
    progressBar.innerText = current + "/" + total
    progressBar.style.width = percentage + "%"
  }

  private showError(container: HTMLElement, message: string): void {
    container.innerHTML = message
    container.classList.remove("alert-info")
    container.classList.add("alert-danger")
  }

  private showPageAmount(
    progressInfo: HTMLElement,
    successContainer: HTMLElement,
    amount: number
  ): void {
    progressInfo.classList.add("hide")
    successContainer.classList.remove("hide")
    successContainer.innerHTML = successContainer.innerHTML.replace(
      "%d",
      String(amount)
    )
  }

  private showSuccess(progressBar: HTMLDivElement, total: number): void {
    progressBar.classList.remove("progress-bar-info")
    progressBar.classList.add("progress-bar-success")
    progressBar.style.width = "100%"
    progressBar.innerHTML = `${total} pages have been indexed.`
  }

  private toggleButtons(disabled: boolean) {
    let buttons = document.querySelectorAll(
      ".js-crawler-button"
    ) as NodeListOf<HTMLButtonElement>
    buttons.forEach((button) => {
      button.disabled = disabled
    })
  }

  private removeSavedProgress(site: string, language: string): void {
    document.querySelector(`#saved-progress-${site}-${language}`)?.remove()
  }

  private getProgressContainer(
    site: string,
    language: string
  ): HTMLElement | null {
    return document.querySelector(
      `#progress-${site}-${language}`
    ) as HTMLElement | null
  }

  private postRequest(
    url: string,
    data: Record<string, any>
  ): Promise<AjaxResponse> {
    return new AjaxRequest(url).post(data, {
      headers: { "Content-Type": "application/json" },
    })
  }
}

export default new Crawler()
