export type PaperAttributes = {
  keyword?: string
  synonyms?: string
  title?: string
  titleWidth?: number
  description?: string
  slug?: string
  favicon?: string
  locale?: string
}

export type YoastAnalysisResultItem = {
  identifier: string
  score: number
  text: string
}

export type YoastAnalysisResult = {
  score: number
  results: YoastAnalysisResultItem[]
}

export type YoastAnalysis = Record<
  string,
  YoastAnalysisResult | Record<string, YoastAnalysisResult>
>

export type MappedResult = {
  score: number
  rating: string
  hasMarks: boolean
  marker: string[]
  id: string
  text: string
  markerId: string
}

export type MappedResults = {
  errorsResults: MappedResult[]
  problemsResults: MappedResult[]
  improvementsResults: MappedResult[]
  goodResults: MappedResult[]
  considerationsResults: MappedResult[]
}
