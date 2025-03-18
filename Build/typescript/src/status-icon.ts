import Modal from "@typo3/backend/modal.js"
import $ from "jquery"
import {
  getResult,
  getScoreFromResult,
  mapResults,
} from "@yoast/yoast-seo-for-typo3/helpers/results.js"
import store from "@yoast/yoast-seo-for-typo3/store.js"
import { State } from "@yoast/yoast-seo-for-typo3/types/yoast"
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
    const container = document.querySelector("h1")
    if (!container) return

    let scoreBar = document.createElement("div")
    scoreBar.classList.add("yoast-seo-score-bar")
    ;["readability", "seo"].forEach((resultType) => {
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
      element.setAttribute("analysis-done", "true")
      element.setAttribute(
        "score",
        getScoreFromResult(state.analysis!!, resultType, resultSubtype)
      )

      if (element.getAttribute("details")) {
        element.onclick = () => {
          this.analysisDetailModal(state, resultType, resultSubtype as string)
        }
      }
    })
  }

  private analysisDetailModal(
    state: State,
    resultType: string,
    resultSubtype: string | null
  ): void {
    const result = getResult(state.analysis!!, resultType, resultSubtype)
    if (result === undefined) return

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
        styleLink.setAttribute("rel", "stylesheet")
        styleLink.setAttribute(
          "href",
          YoastConfiguration.getUrl("yoastCss") as string
        )
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
    iconElement.setAttribute("result-type", resultType)
    iconElement.setAttribute("result-sub-type", resultSubType)
    iconElement.setAttribute("text", text)
    if (classes !== "") {
      iconElement.classList.add(classes)
    }
    return iconElement
  }
}

export default new StatusIcon()
