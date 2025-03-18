import DebounceEvent from "@typo3/core/event/debounce-event.js";
import analysis from "@yoast/yoast-seo-for-typo3/analysis.js";
import FormEngine from "@yoast/yoast-seo-for-typo3/helpers/form-engine.js";
import store from "@yoast/yoast-seo-for-typo3/store.js";
import YoastConfiguration from "@yoast/yoast-seo-for-typo3/yoast-configuration.js";
class FocusKeyphrase {
    constructor() {
        this.initialized = false;
        store.subscribe((state) => {
            if (!state.analysis)
                return;
            if (this.initialized)
                return;
            this.initialized = true;
            this.initializeFocusKeywordField();
            this.initializeSynonymsField();
        });
    }
    initializeFocusKeywordField() {
        ;
        FormEngine.getElements("focusKeyword")?.forEach((element) => {
            element.addEventListener("input", new DebounceEvent("input", () => {
                YoastConfiguration.setFocusKeyword(element.value);
                analysis.refresh();
            }, 500).bindTo(element));
        });
    }
    initializeSynonymsField() {
        ;
        FormEngine.getElements("focusKeywordSynonyms")?.forEach((element) => {
            element.addEventListener("input", new DebounceEvent("input", () => {
                YoastConfiguration.setFocusKeywordSynonyms(element.value);
                analysis.refresh();
            }, 500).bindTo(element));
        });
    }
}
export default new FocusKeyphrase();
