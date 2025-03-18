export type YoastLinkingSuggestions = {
  hasMetaDescription: boolean
  hasTitle: boolean
  prominentWords: Array<{
    word: string
    stem: string
    occurrences: number
  }>
}
