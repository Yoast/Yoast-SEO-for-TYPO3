import DebounceEvent from "@typo3/core/event/debounce-event.js"
import {
  getResult,
  mapResults,
} from "@yoast/yoast-seo-for-typo3/helpers/results.js"
import store from "@yoast/yoast-seo-for-typo3/store.js"
import { State } from "@yoast/yoast-seo-for-typo3/types/yoast"
import YoastConfiguration from "@yoast/yoast-seo-for-typo3/yoast-configuration.js"

export default class AnalysisResult {
  constructor() {
    this.initializePanels()
  }

  init(): void {
    store.subscribe((state) => {
      if (state.analysis) {
        this.updateAnalysisElements(state)
      }
    })
  }

  private updateAnalysisElements(state: State): void {
    document
      .querySelectorAll<HTMLElement>("yoast-analysis-result")
      .forEach((element: HTMLElement) => {
        this.updateElementWithResult(element, state)
      })
  }

  private initializePanels(): void {
    const relatedKeywordFieldSelector =
      YoastConfiguration.getFieldSelector("relatedKeyword")
    if (!relatedKeywordFieldSelector) return

    const relatedKeywordField = document.querySelector<HTMLElement>(
      "#" + relatedKeywordFieldSelector
    )
    if (!relatedKeywordField) return

    relatedKeywordField
      .querySelectorAll<HTMLElement>(`.panel-heading`)
      .forEach((item) => {
        new DebounceEvent(
          "click",
          () => {
            document
              .querySelectorAll<HTMLElement>("yoast-analysis-result")
              .forEach((element: HTMLElement) => {
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
    const result = getResult(state.analysis!, resultType, resultSubtype)
    if (result === undefined) return

    const analysis = mapResults(result.results)
    element.setAttribute("analysis", JSON.stringify(analysis))
  }
}
