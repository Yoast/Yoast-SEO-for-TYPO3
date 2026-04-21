import FrontendRequest from "@maxserv/frontend-request/frontend-request.js";
import AjaxRequest from "@typo3/core/ajax/ajax-request.js";
import DocumentService from "@typo3/core/document-service.js";
import analysis from "@yoast/yoast-seo-for-typo3/analysis.js";
import store from "@yoast/yoast-seo-for-typo3/store.js";
import YoastConfiguration from "@yoast/yoast-seo-for-typo3/yoast-configuration.js";
// Importing web components to ensure they are registered and can be used in the DOM.
// @ts-ignore-next-line
import "@yoast/yoast-seo-for-typo3/dist/webcomponents.js";
import AnalysisResult from "@yoast/yoast-seo-for-typo3/analysis-result.js";
import FocusKeyphrase from "@yoast/yoast-seo-for-typo3/focus-keyphrase.js";
import { saveProminentWords } from "@yoast/yoast-seo-for-typo3/helpers/prominent-words.js";
import { getScoreFromResult, scoreToRating, } from "@yoast/yoast-seo-for-typo3/helpers/results.js";
import SnippetPreview from "@yoast/yoast-seo-for-typo3/snippet-preview.js";
import StatusIcon from "@yoast/yoast-seo-for-typo3/status-icon.js";
class YoastPlugin {
    initialize(configuration) {
        YoastConfiguration.setFromInitialization(configuration);
        this.initializeState();
        new FocusKeyphrase().init();
        new SnippetPreview().init();
        if (YoastConfiguration.isAnalysisEnabled()) {
            new StatusIcon().init();
            new AnalysisResult().init();
        }
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
            if ("error" in data) {
                store.setState({
                    error: data.error,
                });
                return;
            }
            store.setState({ content: data });
            if (!YoastConfiguration.isAnalysisEnabled()) {
                return;
            }
            await analysis.refresh();
            this.saveScores();
            this.saveProminentWords();
        })
            .catch(() => {
            store.setState({
                error: {
                    reason: "Unknown",
                    url: requestData.pageId.toString(),
                    statusCode: 0,
                },
            });
        });
    }
    saveScores() {
        if (!store.getState().analysis)
            return;
        const analysis = store.getState().analysis;
        const readabilityScore = getScoreFromResult(analysis, "readability", null);
        const seoScore = getScoreFromResult(analysis, "seo", "");
        new AjaxRequest(YoastConfiguration.getUrl("saveScores")).post({
            table: YoastConfiguration.getData("table"),
            uid: YoastConfiguration.getData("uid"),
            languageId: YoastConfiguration.getData("languageId"),
            readabilityScore: scoreToRating(parseInt(readabilityScore)),
            seoScore: scoreToRating(parseInt(seoScore)),
        }, { headers: { "Content-Type": "application/json" } });
    }
    saveProminentWords() {
        if (!store.getState().content)
            return;
        analysis
            .runResearch("getProminentWordsForInternalLinking")
            .then((response) => {
            saveProminentWords(YoastConfiguration.getUrl("prominentWords"), YoastConfiguration.getData("table"), YoastConfiguration.getData("uid"), YoastConfiguration.getData("languageId"), response.result);
        });
    }
}
export default new YoastPlugin();
