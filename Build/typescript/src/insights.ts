import analysis from "@yoast/yoast-seo-for-typo3/analysis.js"
import { setAttributes } from "@yoast/yoast-seo-for-typo3/helpers/attributes.js"
import store from "@yoast/yoast-seo-for-typo3/store.js"
import {
  State,
  YoastFleschReadingScore,
  YoastInsights,
  YoastReadingTime,
  YoastWordCount,
} from "@yoast/yoast-seo-for-typo3/types/yoast"

class Insights {
  private cachedAnalysis: State["analysis"] | null = null
  init(): void {
    store.subscribe((state) => {
      if (!state.content || !state.analysis) return
      if (this.cachedAnalysis !== state.analysis) {
        this.cachedAnalysis = state.analysis
        this.updateInsights()
        this.updateFleschReadingScore()
        this.updateReadingTime()
        this.updateWordCount()
      }
    })
  }

  private async updateInsights(): Promise<void> {
    const insightsComponent = document.querySelector("yoast-insights")
    if (!insightsComponent) return

    try {
      const response = await analysis.runResearch<YoastInsights>(
        "getProminentWordsForInsights"
      )
      insightsComponent.setAttribute(
        "keywords",
        this.getKeywords(response.result)
      )
    } catch (error) {
      console.error("Error fetching prominent words for insights:", error)
    }
  }

  private getKeywords(keywords: YoastInsights): string {
    return JSON.stringify(
      keywords.map((item) => ({
        word: item.word,
        occurrences: item.occurrences,
      }))
    )
  }

  private async updateFleschReadingScore(): Promise<void> {
    const fleschComponent = document.querySelector<HTMLElement>(
      "yoast-flesch-reading-score"
    )
    if (!fleschComponent) return

    try {
      const response = await analysis.runResearch<YoastFleschReadingScore>(
        "getFleschReadingScore"
      )
      if (!response.result) {
        fleschComponent.setAttribute("unsupportedLanguage", "true")
      } else {
        fleschComponent.removeAttribute("unsupportedLanguage")
        setAttributes(fleschComponent, {
          score: String(response.result.score),
          difficulty: String(response.result.difficulty),
        })
      }
    } catch (error) {
      console.error("Error fetching Flesch reading score:", error)
    }
  }

  private async updateReadingTime(): Promise<void> {
    const readingTimeComponent = document.querySelector("yoast-reading-time")
    if (!readingTimeComponent) return

    try {
      const response =
        await analysis.runResearch<YoastReadingTime>("readingTime")
      readingTimeComponent.setAttribute("reading-time", String(response.result))
    } catch (error) {
      console.error("Error fetching reading time:", error)
    }
  }

  private async updateWordCount(): Promise<void> {
    const wordCountComponent =
      document.querySelector<HTMLElement>("yoast-word-count")
    if (!wordCountComponent) return

    try {
      const response =
        await analysis.runResearch<YoastWordCount>("wordCountInText")
      setAttributes(wordCountComponent, {
        count: String(response.result.count),
        unit: response.result.unit,
      })
    } catch (error) {
      console.error("Error fetching word count in text:", error)
    }
  }
}

new Insights().init()
