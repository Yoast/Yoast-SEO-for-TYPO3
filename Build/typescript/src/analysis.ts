import measureTextWidth from "@yoast/yoast-seo-for-typo3/helpers/text-width.js"
import Paper from "@yoast/yoast-seo-for-typo3/paper.js"
import Result from "@yoast/yoast-seo-for-typo3/request/result"
import store from "@yoast/yoast-seo-for-typo3/store.js"
import {
  YoastAnalysis,
  YoastContent,
  YoastKeyphrase,
} from "@yoast/yoast-seo-for-typo3/types/yoast"
import worker from "@yoast/yoast-seo-for-typo3/web-worker.js"
import YoastConfiguration from "@yoast/yoast-seo-for-typo3/yoast-configuration.js"

class Analysis {
  private static instance: Analysis

  private constructor() {}

  public static getInstance(): Analysis {
    if (!Analysis.instance) {
      Analysis.instance = new Analysis()
    }
    return Analysis.instance
  }

  public async refresh(): Promise<void> {
    const state = store.getState()
    if (!state.content) return

    const paper = this.createSerializedPaper(
      state.content,
      state.focusKeyphrase
    )
    await this.configureWorker(state.content)

    try {
      const response = await this.analyzePaper(paper)
      if (this.hasRelatedKeyphrases()) {
        const related = await this.analyzeRelatedKeyphrases(paper)
        response.result.seo = { ...response.result.seo, ...related.result.seo }
      }
      store.setState({ analysis: response.result })
    } catch (error) {
      console.error("yoast-seo analysis failed", error)
    }
  }

  private async configureWorker(content: YoastContent): Promise<void> {
    await worker.set(content.cornerstone, content.locale)
  }

  private async analyzePaper(paper: object): Promise<Result<YoastAnalysis>> {
    return await worker.sendRequest<YoastAnalysis>("analyze", { paper })
  }

  private async analyzeRelatedKeyphrases(
    paper: object
  ): Promise<Result<YoastAnalysis>> {
    return await worker.sendRequest<YoastAnalysis>("analyzeRelatedKeywords", {
      paper,
      relatedKeywords: YoastConfiguration.getRelatedKeyphrases(),
    })
  }

  private hasRelatedKeyphrases(): boolean {
    return YoastConfiguration.getRelatedKeyphrases() !== null
  }

  private createSerializedPaper(
    content: YoastContent,
    focusKeyphrase: YoastKeyphrase | null
  ): object {
    const paper = new Paper(content.body, {
      keyword: focusKeyphrase?.keyword ?? "",
      title: content.title,
      synonyms: focusKeyphrase?.synonyms ?? "",
      description: content.metadata.description ?? "",
      locale: content.locale,
      titleWidth: measureTextWidth(content.title),
    })
    return paper.serialize()
  }
}

const analysis = Analysis.getInstance()
export default analysis
