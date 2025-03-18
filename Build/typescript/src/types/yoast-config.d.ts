type YoastUrls = {
    workerUrl: string
    previewUrl: string
    saveScores: string
    prominentWords: string
}

type YoastKeyphrase = {
    keyword: string
    synonyms: string
}

type YoastData = {
    table: string
    uid: number
    pid: number
    languageId: number
}

type YoastFields = {
    title: string
    pageTitle: string
    description: string
    focusKeyword: string
    focusKeywordSynonyms: string
    cornerstone: string
    relatedKeyword: string
}

type YoastConfig = {
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
}
