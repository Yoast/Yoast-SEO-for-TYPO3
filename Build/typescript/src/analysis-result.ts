import {
  getResult,
  mapResults,
} from "@yoast/yoast-seo-for-typo3/helpers/results.js"
import store from "@yoast/yoast-seo-for-typo3/store.js"
import { State } from "@yoast/yoast-seo-for-typo3/types/yoast"

class AnalysisResult {
  constructor() {
    store.subscribe((state) => {
      state.analysis && this.updateAnalysisElements(state)
    })
  }

  private updateAnalysisElements(state: State): void {
    document.querySelectorAll("yoast-analysis-result").forEach((element) => {
      const resultType = (element.getAttribute("result-type") ?? "").toString()
      let resultSubtype = element.getAttribute("result-sub-type")
      if (resultSubtype === null && resultType === "seo") {
        resultSubtype = ""
      }
      const result = getResult(state.analysis!!, resultType, resultSubtype)
      if (result === undefined) return

      const analysis = mapResults(result.results)
      element.setAttribute("analysis", JSON.stringify(analysis))
    })
  }
}

export default new AnalysisResult()
