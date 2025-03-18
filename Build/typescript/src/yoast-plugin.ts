import FrontendRequest from "@maxserv/frontend-request/frontend-request.js"
import DocumentService from "@typo3/core/document-service.js"
import analysis from "@yoast/yoast-seo-for-typo3/analysis.js"
import store from "@yoast/yoast-seo-for-typo3/store.js"
import YoastConfiguration from "@yoast/yoast-seo-for-typo3/yoast-configuration.js"

import "@yoast/yoast-seo-for-typo3/dist/webcomponents.js"
import "@yoast/yoast-seo-for-typo3/snippet-preview.js"
import "@yoast/yoast-seo-for-typo3/status-icon.js"
import "@yoast/yoast-seo-for-typo3/analysis-result.js"

import {
  State,
  YoastConfig,
  YoastKeyphrase,
  YoastRequestData,
} from "@yoast/yoast-seo-for-typo3/types/yoast"

class YoastPlugin {
  public initialize(configuration: YoastConfig): void {
    YoastConfiguration.setFromInitialization(configuration)

    this.initializeState()

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
      .then(async (response: any) => {
        const data = await response.resolve()
        store.setState({ content: data })
        await analysis.refresh()
      })
      .catch(() => {
        store.setState({
          error: true,
        })
      })
  }
}

export default new YoastPlugin()
