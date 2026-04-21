import DebounceEvent from "@typo3/core/event/debounce-event.js"
import analysis from "@yoast/yoast-seo-for-typo3/analysis.js"
import FormEngine from "@yoast/yoast-seo-for-typo3/helpers/form-engine.js"
import { YoastFields } from "@yoast/yoast-seo-for-typo3/types/yoast"
import YoastConfiguration from "@yoast/yoast-seo-for-typo3/yoast-configuration.js"

export default class FocusKeyphrase {
  init(): void {
    this.initializeFocusKeywordField()
    this.initializeSynonymsField()
  }

  private initializeFocusKeywordField(): void {
    this.initializeEventListenerOnField("focusKeyword", (value) => {
      YoastConfiguration.setFocusKeyword(value)
    })
  }
  private initializeSynonymsField(): void {
    this.initializeEventListenerOnField("focusKeywordSynonyms", (value) => {
      YoastConfiguration.setFocusKeywordSynonyms(value)
    })
  }

  private initializeEventListenerOnField(
    fieldName: keyof YoastFields,
    callback: (value: string) => void
  ): void {
    FormEngine.getElements<HTMLInputElement>(fieldName)?.forEach((element) => {
      element.addEventListener(
        "input",
        new DebounceEvent(
          "input",
          () => {
            callback(element.value)
            analysis.refresh()
          },
          500
        ).bindTo(element)
      )
    })
  }
}
