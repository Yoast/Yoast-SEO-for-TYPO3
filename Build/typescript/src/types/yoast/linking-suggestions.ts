export type YoastLinkingSuggestions = {
  hasMetaDescription: boolean
  hasTitle: boolean
  prominentWords: Array<{
    word: string
    stem: string
    occurrences: number
  }>
}

export type YoastLinkingSuggestionResult = {
  excludedPage: number
  languageId: number
  links: {
    [key: string]: {
      active: boolean
      cornerstone: number
      id: number
      label: string
      recordType: string
      score: number
      table: string
    }
  }
}
