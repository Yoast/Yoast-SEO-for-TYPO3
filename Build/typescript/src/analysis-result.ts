import DebounceEvent from "@typo3/core/event/debounce-event.js"
import {
  getResult,
  mapResults,
} from "@yoast/yoast-seo-for-typo3/helpers/results.js"
import store from "@yoast/yoast-seo-for-typo3/store.js"
import { State } from "@yoast/yoast-seo-for-typo3/types/yoast"
import YoastConfiguration from "@yoast/yoast-seo-for-typo3/yoast-configuration.js"

class AnalysisResult {
  private panelsInitialized: boolean = false
  constructor() {
    store.subscribe((state) => {
      state.analysis && this.updateAnalysisElements(state)
      if (!this.panelsInitialized) {
        this.initializePanels()
        this.panelsInitialized = true
      }
    })
  }

  private updateAnalysisElements(state: State): void {
    ;(
      document.querySelectorAll(
        "yoast-analysis-result"
      ) as NodeListOf<HTMLElement>
    ).forEach((element: HTMLElement) => {
      this.updateElementWithResult(element, state)
    })
  }

  private initializePanels(): void {
    const relatedKeywordFieldSelector =
      YoastConfiguration.getFieldSelector("relatedKeyword")
    if (!relatedKeywordFieldSelector) return

    const relatedKeywordField = document.querySelector(
      "#" + relatedKeywordFieldSelector
    ) as HTMLElement | null
    if (!relatedKeywordField) return

    relatedKeywordField.querySelectorAll(`.panel-heading`).forEach((item) => {
      new DebounceEvent(
        "click",
        (e: MouseEvent) => {
          ;(
            document.querySelectorAll(
              "yoast-analysis-result"
            ) as NodeListOf<HTMLElement>
          ).forEach((element: HTMLElement) => {
            this.updateElementWithResult(element, store.getState())
          })
        },
        100
      ).bindTo(item)
    })
  }

  private updateElementWithResult(element: HTMLElement, state: State): void {
    const resultType = (element.getAttribute("result-type") ?? "").toString()
    let resultSubtype = element.getAttribute("result-sub-type")
    if (resultSubtype === null && resultType === "seo") {
      resultSubtype = ""
    }
    const result = getResult(state.analysis!!, resultType, resultSubtype)
    if (result === undefined) return

    const analysis = mapResults(result.results)
    element.setAttribute("analysis", JSON.stringify(analysis))
  }
}

export default new AnalysisResult()
