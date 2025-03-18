import DebounceEvent from "@typo3/core/event/debounce-event.js"
import analysis from "@yoast/yoast-seo-for-typo3/analysis.js"
import FormEngine from "@yoast/yoast-seo-for-typo3/helpers/form-engine.js"
import store from "@yoast/yoast-seo-for-typo3/store.js"
import YoastConfiguration from "@yoast/yoast-seo-for-typo3/yoast-configuration.js"

interface ProgressBarItem {
  input: HTMLInputElement
  storeKey: "title" | "description"
  defaultValue: string
}

class TitleDescription {
  private seoTitleInitialized = false
  private progressBarsInitialized = false

  constructor() {
    store.subscribe(() => {
      this.initializeSeoTitle()
      this.initializeProgressBars()
    })
  }

  initializeSeoTitle(): void {
    if (this.seoTitleInitialized) return
    this.seoTitleInitialized = true

    const titleField = YoastConfiguration.getFieldSelector("title")
    const pageTitleField = YoastConfiguration.getFieldSelector("pageTitle")
    if (!titleField || !pageTitleField) return

    const pageTitleElement = FormEngine.getElement("pageTitle")
    if (pageTitleElement) {
      pageTitleElement.addEventListener("change", () => {
        this.setSeoTitlePlaceholder()
      })
    }
    this.setSeoTitlePlaceholder()
    this.initializeSeoTabLoad()
  }

  initializeProgressBars(): void {
    if (this.progressBarsInitialized || !store.getState().content) return
    const titleField = FormEngine.getElement("title")
    const descriptionField = FormEngine.getElement("description")

    if (!titleField || !descriptionField) return
    this.progressBarsInitialized = true

    const progressBarItems: ProgressBarItem[] = [
      {
        input: titleField as HTMLInputElement,
        storeKey: "title",
        defaultValue: store.getState().content?.title || "",
      },
      {
        input: descriptionField as HTMLInputElement,
        storeKey: "description",
        defaultValue: store.getState().content?.metadata.description || "",
      },
    ]

    progressBarItems.forEach(({ input, storeKey, defaultValue }) => {
      const progressBar = this.addProgressBar(input, storeKey, defaultValue)
      input.addEventListener(
        "input",
        new DebounceEvent(
          "input",
          () => {
            let value = input.value
            if (storeKey === "title") {
              const pageTitleValue =
                FormEngine.getInputElementValue("pageTitle")
              if (value === "") {
                value = pageTitleValue
              }
              value = this.getTitleValue(value)
            }
            progressBar.setAttribute(storeKey, value)
            if (storeKey === "description") {
              store.updateContent({ metadata: { description: value } })
            } else {
              store.updateContent({ [storeKey]: value })
            }
          },
          100
        ).bindTo(input)
      )
      input.addEventListener("change", () => {
        analysis.refresh()
      })
    })
  }

  addProgressBar(
    input: HTMLInputElement,
    storeKey: "title" | "description",
    defaultValue: string
  ): HTMLElement {
    const container = document.createElement("div")
    const progressBar = document.createElement(`yoast-${storeKey}-progress-bar`)
    progressBar.setAttribute(storeKey, defaultValue)
    container.insertAdjacentElement("afterbegin", progressBar)

    container.style.gridArea = "bottom"
    let formControls = input.closest(".form-control-wrap") as HTMLElement
    if (formControls.style.maxWidth) {
      container.style.maxWidth = formControls.style.maxWidth
    }
    input.closest(".form-control-wrap")?.after(container)

    return progressBar
  }

  setSeoTitlePlaceholder(): void {
    const seoTitleElement = FormEngine.getElement("title")
    const pageTitleElement = FormEngine.getElement(
      "pageTitle"
    ) as HTMLInputElement
    if (seoTitleElement && pageTitleElement) {
      const titleValue = pageTitleElement.value
      seoTitleElement.setAttribute("placeholder", titleValue)
    }
  }

  initializeSeoTabLoad(): void {
    document.querySelectorAll(`li.t3js-tabmenu-item`).forEach((item) => {
      if (!item.innerHTML.includes("SEO")) {
        return
      }
      new DebounceEvent(
        "click",
        (e: MouseEvent) => {
          this.initializeProgressBars()

          let value = FormEngine.getInputElementValue("title")
          let pageTitleValue = FormEngine.getInputElementValue("pageTitle")

          FormEngine.getElement("title")?.setAttribute(
            "placeholder",
            pageTitleValue
          )

          if (value === "") {
            value = pageTitleValue
          }
          value = this.getTitleValue(value)
          store.updateContent({ title: value })
        },
        100
      ).bindTo(item)
    })
  }

  getTitleValue(value: string): string {
    const titleConfiguration = store.getState().content?.titleConfiguration
    if (!titleConfiguration) return value

    if (titleConfiguration.append) {
      value += titleConfiguration.append
    }
    if (titleConfiguration.prepend) {
      value = titleConfiguration.prepend + value
    }
    return value
  }
}

export default new TitleDescription()
