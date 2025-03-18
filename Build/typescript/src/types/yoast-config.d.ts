interface YoastConfig {
  fieldSelectors: string[]
  urls: {
    workerUrl: string
    previewUrl: string
    saveScores: string
    prominentWords: string
  }
  isCornerstoneContent: boolean
  focusKeyphrase: {
    keyword: string
    synonyms: string
  }
  translations: object
  supportedLanguages: string[]
}
