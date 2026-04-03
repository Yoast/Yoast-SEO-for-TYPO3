import Modal from "@typo3/backend/modal.js"
import $ from "jquery"
import { setAttributes } from "@yoast/yoast-seo-for-typo3/helpers/attributes.js"
import {
  getResult,
  getScoreFromResult,
  mapResults,
} from "@yoast/yoast-seo-for-typo3/helpers/results.js"
import store from "@yoast/yoast-seo-for-typo3/store.js"
import {
  State,
  YoastAnalysisResult,
} from "@yoast/yoast-seo-for-typo3/types/yoast"
import YoastConfiguration from "@yoast/yoast-seo-for-typo3/yoast-configuration.js"

class StatusIcon {
  private initialized: boolean = false

  constructor() {
    store.subscribe((state) => {
      if (!this.initialized) {
        this.initializeAnalysisElements()
        this.initialized = true
      }

      state.analysis && this.updateAnalysisElements(state)
    })
  }

  private initializeAnalysisElements(): void {
    if (YoastConfiguration.isTCA()) {
      this.initializeTCAHeaderIcons()
    }
    this.initializeAnalysisFieldIcons()
  }

  private initializeTCAHeaderIcons(): void {
    const container =
      document.querySelector("h1") ??
      document.querySelector(".contextual-record-edit-body")
    if (!container) return

    let scoreBar = document.createElement("div")
    scoreBar.classList.add("yoast-seo-score-bar")
    ;["readability", "seo", "inclusiveLanguage"].forEach((resultType) => {
      scoreBar.append(
        this.getIconElement(
          resultType,
          "",
          "true",
          "yoast-seo-score-bar--analysis"
        )
      )
    })

    container.parentNode?.insertBefore(scoreBar, container.nextSibling)
  }

  private initializeAnalysisFieldIcons(): void {
    document.querySelectorAll("yoast-analysis-result").forEach((container) => {
      const formSection = container.closest(".form-section")
      if (formSection === null) return

      const resultType = container.getAttribute("result-type") || ""
      const resultSubType = container.getAttribute("result-sub-type") || ""
      const icon = this.getIconElement(resultType, resultSubType)

      const heading = formSection.querySelector("h3")
      if (heading) {
        heading.prepend(icon)
        return
      }

      const panelIcon = container.closest(".panel")?.querySelector(".t3js-icon")
      if (panelIcon) {
        panelIcon.replaceWith(icon)
      }
    })
  }

  private updateAnalysisElements(state: State): void {
    ;(
      document.querySelectorAll("yoast-status-icon") as unknown as HTMLElement[]
    ).forEach((element) => {
      const resultType = (element.getAttribute("result-type") ?? "").toString()
      let resultSubtype = element.getAttribute("result-sub-type")
      if (resultSubtype === null && resultType === "seo") {
        resultSubtype = ""
      }
      const result = getResult(state.analysis!!, resultType, resultSubtype)
      setAttributes(element, {
        "analysis-done": "true",
        score: getScoreFromResult(state.analysis!!, resultType, resultSubtype),
      })
      if (
        element.getAttribute("details") &&
        element.getAttribute("details") === "true"
      ) {
        element.onclick = () => {
          this.analysisDetailModal(resultType, result)
        }
      }
    })
  }

  private analysisDetailModal(
    resultType: string,
    result: YoastAnalysisResult | undefined = undefined
  ): void {
    if (result === undefined) return
    if (result.results.length === 0) return

    const analysis = mapResults(result.results)
    const $content = $("<yoast-analysis-result>").attr(
      "analysis",
      JSON.stringify(analysis)
    )
    Modal.advanced({
      title: this.getModalTitle(resultType),
      content: $content,
      additionalCssClasses: ["yoast-modal"],
      callback: (modal: HTMLElement) => {
        const styleLink = document.createElement("link")
        setAttributes(styleLink, {
          rel: "stylesheet",
          href: YoastConfiguration.getUrl("yoastCss") as string,
        })
        modal.appendChild(styleLink)
      },
    })
  }

  private getModalTitle(resultType: string): string {
    const selector =
      resultType === "readability"
        ? ".score-label-readability"
        : resultType === "seo"
          ? ".score-label-seo"
          : resultType === "inclusiveLanguage"
            ? ".score-label-inclusiveLanguage"
            : null

    if (!selector) return ""

    const element = document.querySelector<HTMLElement>(selector)
    if (!element) return ""

    let title = element.innerText.slice(0, -1)

    if (resultType === "seo") {
      const keyword = YoastConfiguration.getFocusKeyphrase()?.keyword
      if (keyword) title += `: ${keyword}`
    }

    return title
  }

  private getIconElement(
    resultType: string,
    resultSubType: string,
    text: "false" | "true" = "false",
    classes: string = ""
  ): HTMLElement {
    let iconElement = document.createElement("yoast-status-icon")
    setAttributes(iconElement, {
      "result-type": resultType,
      "result-sub-type": resultSubType,
      text: text,
    })
    if (classes !== "") {
      iconElement.classList.add(classes)
    }
    return iconElement
  }
}

export default new StatusIcon()
