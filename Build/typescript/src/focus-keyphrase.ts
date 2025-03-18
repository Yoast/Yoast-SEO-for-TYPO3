import DebounceEvent from "@typo3/core/event/debounce-event.js"
import analysis from "@yoast/yoast-seo-for-typo3/analysis.js"
import FormEngine from "@yoast/yoast-seo-for-typo3/helpers/form-engine.js"
import store from "@yoast/yoast-seo-for-typo3/store.js"
import YoastConfiguration from "@yoast/yoast-seo-for-typo3/yoast-configuration.js"

class FocusKeyphrase {
  private initialized: boolean = false
  constructor() {
    store.subscribe((state) => {
      if (!state.analysis) return
      if (this.initialized) return
      this.initialized = true
      this.initializeFocusKeywordField()
      this.initializeSynonymsField()
    })
  }

  private initializeFocusKeywordField(): void {
    ;(
      FormEngine.getElements("focusKeyword") as NodeListOf<HTMLInputElement>
    )?.forEach((element) => {
      element.addEventListener(
        "input",
        new DebounceEvent(
          "input",
          () => {
            YoastConfiguration.setFocusKeyword(element.value)
            analysis.refresh()
          },
          500
        ).bindTo(element)
      )
    })
  }
  private initializeSynonymsField(): void {
    ;(
      FormEngine.getElements(
        "focusKeywordSynonyms"
      ) as NodeListOf<HTMLInputElement>
    )?.forEach((element) => {
      element.addEventListener(
        "input",
        new DebounceEvent(
          "input",
          () => {
            YoastConfiguration.setFocusKeywordSynonyms(element.value)
            analysis.refresh()
          },
          500
        ).bindTo(element)
      )
    })
  }
}

export default new FocusKeyphrase()
