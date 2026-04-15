import FrontendRequest from "@maxserv/frontend-request/frontend-request.js"
import AjaxRequest from "@typo3/core/ajax/ajax-request.js"
import DocumentService from "@typo3/core/document-service.js"
import analysis from "@yoast/yoast-seo-for-typo3/analysis.js"
import store from "@yoast/yoast-seo-for-typo3/store.js"
import YoastConfiguration from "@yoast/yoast-seo-for-typo3/yoast-configuration.js"

// Importing web components to ensure they are registered and can be used in the DOM.
// @ts-ignore
import "@yoast/yoast-seo-for-typo3/dist/webcomponents.js"

import AnalysisResult from "@yoast/yoast-seo-for-typo3/analysis-result.js"
import FocusKeyphrase from "@yoast/yoast-seo-for-typo3/focus-keyphrase.js"
import { AjaxResponse } from "@yoast/yoast-seo-for-typo3/helpers/ajax-response.js"
import { saveProminentWords } from "@yoast/yoast-seo-for-typo3/helpers/prominent-words.js"
import {
  getScoreFromResult,
  scoreToRating,
} from "@yoast/yoast-seo-for-typo3/helpers/results.js"
import SnippetPreview from "@yoast/yoast-seo-for-typo3/snippet-preview.js"
import StatusIcon from "@yoast/yoast-seo-for-typo3/status-icon.js"
import {
  RequestError,
  State,
  YoastConfig,
  YoastContent,
  YoastKeyphrase,
  YoastLinkingSuggestions,
  YoastRequestData,
} from "@yoast/yoast-seo-for-typo3/types/yoast"

class YoastPlugin {
  public initialize(configuration: YoastConfig): void {
    YoastConfiguration.setFromInitialization(configuration)
    this.initializeState()

    new FocusKeyphrase().init()
    new SnippetPreview().init()
    new StatusIcon().init()
    new AnalysisResult().init()

    DocumentService.ready().then(() => {
      this.previewRequest(configuration.requestData)
    })
  }

  private initializeState(): void {
    let initialState: Partial<State> = {
      siteName: YoastConfiguration.getData("websiteTitle") as string,
    }
    if (YoastConfiguration.getFocusKeyphrase() !== null) {
      initialState.focusKeyphrase =
        YoastConfiguration.getFocusKeyphrase() as YoastKeyphrase
    }
    store.setState(initialState)
  }

  private async previewRequest(requestData: YoastRequestData): Promise<void> {
    FrontendRequest.request(requestData)
      .then(async (response: AjaxResponse) => {
        const data = await response.resolve<YoastContent | RequestError>()
        if ("error" in data) {
          store.setState({
            error: data.error,
          })
          return
        }
        store.setState({ content: data })
        await analysis.refresh()
        this.saveScores()
        this.saveProminentWords()
      })
      .catch(() => {
        store.setState({
          error: {
            reason: "Unknown",
            url: requestData.pageId.toString(),
            statusCode: 0,
          },
        })
      })
  }

  private saveScores(): void {
    if (!store.getState().analysis) return
    const analysis = store.getState().analysis
    const readabilityScore = getScoreFromResult(analysis!, "readability", null)
    const seoScore = getScoreFromResult(analysis!, "seo", "")

    new AjaxRequest(YoastConfiguration.getUrl("saveScores")).post(
      {
        table: YoastConfiguration.getData("table") as string,
        uid: YoastConfiguration.getData("uid") as number,
        languageId: YoastConfiguration.getData("languageId") as number,
        readabilityScore: scoreToRating(parseInt(readabilityScore)),
        seoScore: scoreToRating(parseInt(seoScore)),
      },
      { headers: { "Content-Type": "application/json" } }
    )
  }

  private saveProminentWords(): void {
    if (!store.getState().content) return
    analysis
      .runResearch<YoastLinkingSuggestions>(
        "getProminentWordsForInternalLinking"
      )
      .then((response) => {
        saveProminentWords(
          YoastConfiguration.getUrl("prominentWords") as string,
          YoastConfiguration.getData("table") as string,
          YoastConfiguration.getData("uid") as string,
          YoastConfiguration.getData("languageId") as string,
          response.result
        )
      })
  }
}

export default new YoastPlugin()
