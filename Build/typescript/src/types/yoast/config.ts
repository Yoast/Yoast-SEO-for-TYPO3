import { YoastUrls } from "."

export type YoastKeyphrase = {
  keyword: string | null
  synonyms: string | null
}

export type YoastData = {
  table: string
  uid: number
  pid: number
  languageId: number
  websiteTitle: string
}

export type YoastRequestData = {
  pageId: number
  languageId: number
  additionalGetVars: string
}

export type YoastFields = {
  title: string
  pageTitle: string
  description: string
  focusKeyword: string
  focusKeywordSynonyms: string
  cornerstone: string
  relatedKeyword: string
  ogTitle: string
  ogDescription: string
  ogImage: string
  ogImageContainer: string
  twitterTitle: string
  twitterDescription: string
  twitterImage: string
  twitterImageContainer: string
  twitterCard: string
}

export type YoastConfig = {
  urls: YoastUrls
  analysisEnabled?: boolean
  isCornerstoneContent: boolean
  focusKeyphrase: YoastKeyphrase
  TCA?: boolean
  data: YoastData
  fieldSelectors?: YoastFields
  supportedLanguages: string[]
  inclusiveLanguageEnabled: boolean
  relatedKeyphrases: {
    [key: string]: YoastKeyphrase
  }
  requestData: YoastRequestData
}
