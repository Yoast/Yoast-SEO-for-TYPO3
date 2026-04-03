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
  private inclusiveLanguageEnabled: boolean = false
  private relatedKeyphrases: Record<string, YoastKeyphrase> | null = null

  private constructor() {}

  public static getInstance() {
    if (!YoastConfigurationManager.instance) {
      this.instance = new YoastConfigurationManager()
    }
    return YoastConfigurationManager.instance
  }

  public getUrl(key: keyof YoastUrls): string | null {
    if (!this.urls) {
      return null
    }
    return this.urls[key]
  }

  public setUrl(key: keyof YoastUrls, url: string): void {
    if (!this.urls) {
      this.urls = {} as YoastUrls
    }
    this.urls[key] = url
  }

  public getFieldSelector(fieldName: keyof YoastFields): string | null {
    if (!this.fieldSelectors) {
      return null
    }
    return this.fieldSelectors[fieldName]
  }

  public getFieldSelectors(): YoastFields | null {
    return this.fieldSelectors
  }

  public setFieldSelector(
    fieldName: keyof YoastFields,
    selector: string
  ): void {
    if (!this.fieldSelectors) {
      this.fieldSelectors = {} as YoastFields
    }
    this.fieldSelectors[fieldName] = selector
  }

  public isTCA(): boolean {
    return this.TCA
  }

  public isCornerstone(): boolean {
    return this.cornerstone
  }

  public setCornerstone(isCornerstone: boolean): void {
    this.cornerstone = isCornerstone
  }

  public getData(dataKey: keyof YoastData): number | string | null {
    if (!this.data) {
      return null
    }
    return this.data[dataKey]
  }

  public getSupportedLanguage(locale: string): string {
    if (!this.supportedLanguages) {
      return this.fallbackLanguage
    }

    const languageCode = locale.slice(0, 2)
    return this.supportedLanguages.indexOf(languageCode) !== -1
      ? languageCode
      : this.fallbackLanguage
  }

  public setSupportedLanguages(languages: string[]): void {
    this.supportedLanguages = languages
  }

  public isInclusiveLanguageEnabled(): boolean {
    return this.inclusiveLanguageEnabled
  }

  public getFocusKeyphrase(): YoastKeyphrase | null {
    return this.focusKeyphrase
  }

  public setFocusKeyword(keyword: string): void {
    if (!this.focusKeyphrase) {
      this.focusKeyphrase = { keyword: null, synonyms: null }
    }
    this.focusKeyphrase.keyword = keyword
  }

  public setFocusKeywordSynonyms(synonyms: string): void {
    if (!this.focusKeyphrase) {
      this.focusKeyphrase = { keyword: null, synonyms: null }
    }
    this.focusKeyphrase.synonyms = synonyms
  }

  public getRelatedKeyphrases(): Record<string, YoastKeyphrase> | null {
    return this.relatedKeyphrases
  }

  public setFromInitialization(configuration: YoastConfig): void {
    const keys: (keyof YoastConfig)[] = [
      "urls",
      "isCornerstoneContent",
      "focusKeyphrase",
      "TCA",
      "data",
      "fieldSelectors",
      "supportedLanguages",
      "inclusiveLanguageEnabled",
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
          case "fieldSelectors":
            configuration.fieldSelectors &&
              Object.entries(configuration.fieldSelectors).forEach(
                ([fieldName, selector]) => {
                  this.setFieldSelector(
                    fieldName as keyof YoastFields,
                    selector
                  )
                }
              )
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
