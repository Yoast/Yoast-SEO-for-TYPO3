import "@yoast/yoast-seo-for-typo3/dist/webcomponents.js"

import DocumentService from "@typo3/core/document-service.js"
import { setAttributes } from "@yoast/yoast-seo-for-typo3/helpers/attributes.js"
import FormEngine from "@yoast/yoast-seo-for-typo3/helpers/form-engine.js"
import SocialPreview from "@yoast/yoast-seo-for-typo3/social-preview.js"
import store from "@yoast/yoast-seo-for-typo3/store.js"
import { YoastFields } from "@yoast/yoast-seo-for-typo3/types/yoast"

type FacebookPreviewConfig = {
  fieldSelectors: Partial<Record<keyof YoastFields, string>>
}

class FacebookPreview extends SocialPreview {
  public async initialize(configuration: FacebookPreviewConfig): Promise<void> {
    await DocumentService.ready()

    this.registerFieldSelectors(configuration.fieldSelectors)
    this.setSocialType("facebook")

    this.setupListeners()
    this.syncPreview()
    this.isInitialized = true

    store.subscribe(() => {
      if (this.isInitialized) this.syncPreview()
    })
  }

  private setupListeners(): void {
    const fields: (keyof YoastFields)[] = ["ogTitle", "ogDescription"]

    fields.forEach((fieldName) => {
      FormEngine.getElement<HTMLInputElement>(fieldName)?.addEventListener(
        "input",
        () => this.syncPreview()
      )
    })
  }

  private getData() {
    const ogTitle = FormEngine.getInputElementValue("ogTitle").trim()
    const ogDesc = FormEngine.getInputElementValue("ogDescription").trim()

    const seoTitle = FormEngine.getInputElementValue("title")
    const pageTitle = FormEngine.getInputElementValue("pageTitle")
    const seoDesc = FormEngine.getInputElementValue("description")

    return {
      title: ogTitle || seoTitle || pageTitle || "",
      description: ogDesc || seoDesc || "...",
    }
  }

  private syncPreview(): void {
    const previewElement = this.getElement()
    if (!previewElement) return

    const values = this.getData()

    setAttributes(previewElement, {
      title: values.title,
      description: values.description,
    })
  }
}

export default new FacebookPreview()
