import DebounceEvent from "@typo3/core/event/debounce-event.js";
import { getResult, mapResults, } from "@yoast/yoast-seo-for-typo3/helpers/results.js";
import store from "@yoast/yoast-seo-for-typo3/store.js";
import YoastConfiguration from "@yoast/yoast-seo-for-typo3/yoast-configuration.js";
export default class AnalysisResult {
    constructor() {
        this.initializePanels();
    }
    init() {
        store.subscribe((state) => {
            state.analysis && this.updateAnalysisElements(state);
        });
    }
    updateAnalysisElements(state) {
        document
            .querySelectorAll("yoast-analysis-result")
            .forEach((element) => {
            this.updateElementWithResult(element, state);
        });
    }
    initializePanels() {
        const relatedKeywordFieldSelector = YoastConfiguration.getFieldSelector("relatedKeyword");
        if (!relatedKeywordFieldSelector)
            return;
        const relatedKeywordField = document.querySelector("#" + relatedKeywordFieldSelector);
        if (!relatedKeywordField)
            return;
        relatedKeywordField
            .querySelectorAll(`.panel-heading`)
            .forEach((item) => {
            new DebounceEvent("click", () => {
                document
                    .querySelectorAll("yoast-analysis-result")
                    .forEach((element) => {
                    this.updateElementWithResult(element, store.getState());
                });
            }, 100).bindTo(item);
        });
    }
    updateElementWithResult(element, state) {
        const resultType = (element.getAttribute("result-type") ?? "").toString();
        let resultSubtype = element.getAttribute("result-sub-type");
        if (resultSubtype === null && resultType === "seo") {
            resultSubtype = "";
        }
        const result = getResult(state.analysis, resultType, resultSubtype);
        if (result === undefined)
            return;
        const analysis = mapResults(result.results);
        element.setAttribute("analysis", JSON.stringify(analysis));
    }
}
