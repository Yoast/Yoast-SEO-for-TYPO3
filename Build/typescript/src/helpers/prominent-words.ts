import AjaxRequest from "@typo3/core/ajax/ajax-request.js"
import { YoastLinkingSuggestions } from "@yoast/yoast-seo-for-typo3/types/yoast"

export function saveProminentWords(
  url: string,
  table: string,
  pageId: string,
  language: string,
  result: YoastLinkingSuggestions
): void {
  result.prominentWords.sort((a, b) => b.occurrences - a.occurrences)
  if (result.prominentWords.length > 25) {
    result.prominentWords = result.prominentWords.slice(0, 25)
  }
  let compressedWords: Record<string, number> = {}
  result.prominentWords.forEach((word) => {
    compressedWords[word.stem] = word.occurrences
  })
  new AjaxRequest(url).post(
    {
      table: table,
      uid: pageId,
      languageId: language,
      words: compressedWords,
    },
    {
      headers: { "Content-Type": "application/json" },
    }
  )
}
