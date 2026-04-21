import DocumentService from "@typo3/core/document-service.js"
import { setAttributes } from "@yoast/yoast-seo-for-typo3/helpers/attributes.js"
import FormEngine from "@yoast/yoast-seo-for-typo3/helpers/form-engine.js"
import SocialPreview from "@yoast/yoast-seo-for-typo3/social-preview.js"
import store from "@yoast/yoast-seo-for-typo3/store.js"
import { YoastFields } from "@yoast/yoast-seo-for-typo3/types/yoast"

// @ts-ignore-next-line
import "@yoast/yoast-seo-for-typo3/dist/webcomponents.js"

type TwitterPreviewConfig = {
  fieldSelectors: Partial<Record<keyof YoastFields, string>>
}

class TwitterPreview extends SocialPreview {
  public async initialize(configuration: TwitterPreviewConfig): Promise<void> {
    await DocumentService.ready()

    this.registerFieldSelectors(configuration.fieldSelectors)
    this.setSocialType("twitter")

    this.setupListeners()
    this.syncPreview()
    this.isInitialized = true

    store.subscribe(() => {
      if (this.isInitialized) this.syncPreview()
    })
  }

  private setupListeners(): void {
    const fields = [
      { key: "twitterTitle", type: "input", event: "input" },
      { key: "twitterDescription", type: "input", event: "input" },
      { key: "twitterCard", type: "select", event: "change" },
    ] as const

    fields.forEach(({ key, type, event }) => {
      const element =
        type === "select"
          ? FormEngine.getSelectElement(key)
          : FormEngine.getElement(key)
      element?.addEventListener(event, () => this.syncPreview())
    })
  }

  private syncPreview(): void {
    const previewElement = this.getElement()
    if (!previewElement) return

    const data = this.getData()

    setAttributes(previewElement, {
      title: data.displayTitle,
      description: data.displayDescription,
      "is-large": (data.twitterCard === "summary_large_image").toString(),
    })
  }

  private getData() {
    const twitterTitle = FormEngine.getInputElementValue("twitterTitle").trim()
    const twitterDesc =
      FormEngine.getInputElementValue("twitterDescription").trim()
    const twitterCard = FormEngine.getSelectElementValue("twitterCard")

    const seoTitle = FormEngine.getInputElementValue("title")
    const pageTitle = FormEngine.getInputElementValue("pageTitle")
    const seoDescription = FormEngine.getInputElementValue("description")

    return {
      twitterCard,
      displayTitle: twitterTitle || seoTitle || pageTitle || "",
      displayDescription: twitterDesc || seoDescription || "...",
    }
  }
}

export default new TwitterPreview()
