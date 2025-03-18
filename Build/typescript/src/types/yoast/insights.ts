export type YoastInsights = Array<{
  occurrences: number
  word: string
}>

export type YoastFleschReadingScore = {
  score: number
  difficulty: number
}

export type YoastWordCount = {
  count: number
  text: string
  unit: "word" | "character"
}

export type YoastReadingTime = number
