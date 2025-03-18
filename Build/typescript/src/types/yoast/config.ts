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
}

export type YoastConfig = {
  urls: YoastUrls
  isCornerstoneContent: boolean
  focusKeyphrase: YoastKeyphrase
  TCA?: boolean
  data: YoastData
  fieldSelectors?: YoastFields
  supportedLanguages: string[]
  relatedKeyphrases: {
    [key: string]: YoastKeyphrase
  }
  requestData: YoastRequestData
}
