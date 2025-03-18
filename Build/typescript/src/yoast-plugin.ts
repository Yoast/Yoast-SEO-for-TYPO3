import DocumentService from '@typo3/core/document-service.js';
import AjaxRequest from "@typo3/core/ajax/ajax-request.js";
import YoastConfiguration from "@yoast/yoast-seo-for-typo3/yoast-configuration.js";
import {store} from "@yoast/yoast-seo-for-typo3/store.js";
import {analysis} from "@yoast/yoast-seo-for-typo3/analysis.js";

class YoastPlugin {
  public initialize(configuration: YoastConfig): void {
    YoastConfiguration.setFromInitialization(configuration);
    DocumentService.ready().then(() => {
      this.previewRequest();
    });
  }

  private previewRequest(): void {
    const previewUrl = YoastConfiguration.getUrl('previewUrl');
    if (!previewUrl) {
      return;
    }

    new AjaxRequest(previewUrl)
      .get()
      .then(async (response: any) => {
        const data = await response.resolve();
        store.setState(data);
        analysis.refresh();
      })
      .catch(() => {
        store.setState({
          error: true
        });
      });
  }
}

export default new YoastPlugin();
