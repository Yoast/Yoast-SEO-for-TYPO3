import { YoastFields } from "@yoast/yoast-seo-for-typo3/types/yoast"
import YoastConfiguration from "@yoast/yoast-seo-for-typo3/yoast-configuration.js"

const SOCIAL_CONFIG = {
  facebook: {
    tagName: "yoast-facebook-preview",
    fieldKey: "og_image",
    containerKey: "ogImageContainer",
  },
  twitter: {
    tagName: "yoast-twitter-preview",
    fieldKey: "twitter_image",
    containerKey: "twitterImageContainer",
  },
} as const

type SocialType = keyof typeof SOCIAL_CONFIG

interface PreviewHTMLElement extends HTMLElement {
  onImageClick?: (previewElement: HTMLElement) => void
}

export default abstract class SocialPreview {
  private previewElement: PreviewHTMLElement | null = null
  private observer: MutationObserver | null = null
  private debounceTimer: number | null = null

  protected socialType: SocialType = "facebook"
  protected isInitialized = false

  protected setSocialType(type: SocialType): void {
    this.socialType = type
    this.initializeElement()
  }

  protected getElement(): PreviewHTMLElement | null {
    if (!this.previewElement) {
      this.initializeElement()
    }
    return this.previewElement
  }

  protected registerFieldSelectors(
    selectors: Partial<Record<keyof YoastFields, string>>
  ): void {
    Object.entries(selectors).forEach(([field, selector]) => {
      if (selector) {
        YoastConfiguration.setFieldSelector(
          field as keyof YoastFields,
          selector
        )
      }
    })
  }

  protected initializeElement(): void {
    const config = SOCIAL_CONFIG[this.socialType]
    this.previewElement = document.querySelector<PreviewHTMLElement>(
      config.tagName
    )

    if (!this.previewElement) {
      return
    }

    this.setupImageClickAction()
    this.setupFileObserver()
  }

  private setupImageClickAction(): void {
    if (!this.previewElement) return

    this.previewElement.onImageClick = () => this.triggerFileBrowser()
  }

  private setupFileObserver(): void {
    const containerId = this.getFileContainerSelector()
    const container = containerId ? document.getElementById(containerId) : null

    if (!container) return
    this.disconnectObserver()

    this.observer = new MutationObserver(() => this.debouncedSyncImage())
    this.observer.observe(container, { childList: true, subtree: true })
  }

  protected triggerFileBrowser(): void {
    const { fieldKey, containerKey } = SOCIAL_CONFIG[this.socialType]
    let button = document.querySelector<HTMLButtonElement>(
      `[data-local-field="${fieldKey}"] button`
    )
    if (button === null) {
      const containerFieldSelector =
        YoastConfiguration.getFieldSelector(containerKey)
      button = document.querySelector<HTMLButtonElement>(
        `#${containerFieldSelector} button`
      )
    }
    button?.click()
  }

  protected debouncedSyncImage(): void {
    if (this.debounceTimer) window.clearTimeout(this.debounceTimer)

    this.debounceTimer = window.setTimeout(() => {
      this.syncImageToPreview()
      this.debounceTimer = null
    }, 300)
  }

  protected syncImageToPreview(): void {
    const containerId = this.getFileContainerSelector()
    if (!containerId || !this.previewElement) return

    const img = document.querySelector<HTMLImageElement>(
      `#${containerId} .t3js-image-manipulation-preview img`
    )

    // Fallback to " " to clear the image in the web component
    const src = img?.getAttribute("src") || " "
    this.previewElement.setAttribute("image-url", src)
  }

  private getFileContainerSelector(): string | null {
    const { containerKey } = SOCIAL_CONFIG[this.socialType]
    return YoastConfiguration.getFieldSelector(containerKey)
  }

  public destroy(): void {
    this.disconnectObserver()
    if (this.debounceTimer) window.clearTimeout(this.debounceTimer)
  }

  private disconnectObserver(): void {
    this.observer?.disconnect()
    this.observer = null
  }
}
