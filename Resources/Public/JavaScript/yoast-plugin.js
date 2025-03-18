import FrontendRequest from "@maxserv/frontend-request/frontend-request.js";
import DocumentService from "@typo3/core/document-service.js";
import analysis from "@yoast/yoast-seo-for-typo3/analysis.js";
import store from "@yoast/yoast-seo-for-typo3/store.js";
import YoastConfiguration from "@yoast/yoast-seo-for-typo3/yoast-configuration.js";
import "@yoast/yoast-seo-for-typo3/dist/webcomponents.js";
import "@yoast/yoast-seo-for-typo3/snippet-preview.js";
import "@yoast/yoast-seo-for-typo3/status-icon.js";
import "@yoast/yoast-seo-for-typo3/analysis-result.js";
class YoastPlugin {
    initialize(configuration) {
        YoastConfiguration.setFromInitialization(configuration);
        this.initializeState();
        DocumentService.ready().then(() => {
            this.previewRequest(configuration.requestData);
        });
    }
    initializeState() {
        let initialState = {
            siteName: YoastConfiguration.getData("websiteTitle"),
        };
        if (YoastConfiguration.getFocusKeyphrase() !== null) {
            initialState.focusKeyphrase =
                YoastConfiguration.getFocusKeyphrase();
        }
        store.setState(initialState);
    }
    async previewRequest(requestData) {
        FrontendRequest.request(requestData)
            .then(async (response) => {
            const data = await response.resolve();
            store.setState({ content: data });
            await analysis.refresh();
        })
            .catch(() => {
            store.setState({
                error: true,
            });
        });
    }
}
export default new YoastPlugin();
