import analysis from "@yoast/yoast-seo-for-typo3/analysis.js"
import FormEngine from "@yoast/yoast-seo-for-typo3/helpers/form-engine.js"
import store from "@yoast/yoast-seo-for-typo3/store.js"
import YoastConfiguration from "@yoast/yoast-seo-for-typo3/yoast-configuration.js"

class Cornerstone {
  private initialized = false
  constructor() {
    store.subscribe(() => {
      this.initializeCornerstoneEvent()
    })
  }

  initializeCornerstoneEvent(): void {
    if (this.initialized) return
    this.initialized = true

    const cornerstoneField = FormEngine.getElement("cornerstone")
    if (!cornerstoneField) return

    cornerstoneField.addEventListener("change", (event: Event) => {
      const target = event.target as HTMLInputElement
      const isCornerstone = target.checked
      YoastConfiguration.setCornerstone(isCornerstone)
      analysis.refresh()
    })
  }
}

export default new Cornerstone()
