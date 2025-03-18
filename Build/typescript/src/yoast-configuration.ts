import {
  YoastConfig,
  YoastData,
  YoastFields,
  YoastKeyphrase,
  YoastUrls,
} from "@yoast/yoast-seo-for-typo3/types/yoast"

class YoastConfigurationManager {
  private static instance: YoastConfigurationManager
  private fallbackLanguage: string = "en"

  private urls: YoastUrls | null = null
  private cornerstone: boolean = false
  private focusKeyphrase: YoastKeyphrase | null = null
  private TCA: boolean = false
  private data: YoastData | null = null
  private fieldSelectors: YoastFields | null = null
  private supportedLanguages: string[] | null = null
  private relatedKeyphrases: Record<string, YoastKeyphrase> | null = null

  private constructor() {}

  public static getInstance() {
    if (!YoastConfigurationManager.instance) {
      this.instance = new YoastConfigurationManager()
    }
    return YoastConfigurationManager.instance
  }

  getUrl(key: keyof YoastUrls): string | null {
    if (!this.urls) {
      return null
    }
    return this.urls[key]
  }

  getFieldSelector(fieldName: keyof YoastFields): string | null {
    if (!this.fieldSelectors) {
      return null
    }
    return this.fieldSelectors[fieldName]
  }

  isTCA(): boolean {
    return this.TCA
  }

  isCornerstone(): boolean {
    return this.cornerstone
  }

  getData(dataKey: keyof YoastData): number | string | null {
    if (!this.data) {
      return null
    }
    return this.data[dataKey]
  }

  getSupportedLanguage(locale: string): string {
    if (!this.supportedLanguages) {
      return this.fallbackLanguage
    }

    const languageCode = locale.slice(0, 2)
    return this.supportedLanguages.indexOf(languageCode) !== -1
      ? languageCode
      : this.fallbackLanguage
  }

  getFocusKeyphrase(): YoastKeyphrase | null {
    return this.focusKeyphrase
  }

  getRelatedKeyphrases(): Record<string, YoastKeyphrase> | null {
    return this.relatedKeyphrases
  }

  setFromInitialization(configuration: YoastConfig): void {
    const keys: (keyof YoastConfig)[] = [
      "urls",
      "isCornerstoneContent",
      "focusKeyphrase",
      "TCA",
      "data",
      "fieldSelectors",
      "supportedLanguages",
      "relatedKeyphrases",
    ]

    for (const key of keys) {
      if (configuration[key] !== undefined) {
        switch (key) {
          case "isCornerstoneContent":
            this.cornerstone = configuration.isCornerstoneContent!
            break
          case "TCA":
            this.TCA = configuration.TCA!
            break
          default:
            // @ts-ignore
            this[key] = configuration[key]
        }
      }
    }
  }
}

const YoastConfiguration = YoastConfigurationManager.getInstance()
export default YoastConfiguration
