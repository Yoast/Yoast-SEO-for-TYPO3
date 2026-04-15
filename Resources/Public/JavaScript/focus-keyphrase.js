import DebounceEvent from "@typo3/core/event/debounce-event.js";
import analysis from "@yoast/yoast-seo-for-typo3/analysis.js";
import FormEngine from "@yoast/yoast-seo-for-typo3/helpers/form-engine.js";
import YoastConfiguration from "@yoast/yoast-seo-for-typo3/yoast-configuration.js";
export default class FocusKeyphrase {
    init() {
        this.initializeFocusKeywordField();
        this.initializeSynonymsField();
    }
    initializeFocusKeywordField() {
        this.initializeEventListenerOnField("focusKeyword", (value) => {
            YoastConfiguration.setFocusKeyword(value);
        });
    }
    initializeSynonymsField() {
        this.initializeEventListenerOnField("focusKeywordSynonyms", (value) => {
            YoastConfiguration.setFocusKeywordSynonyms(value);
        });
    }
    initializeEventListenerOnField(fieldName, callback) {
        FormEngine.getElements(fieldName)?.forEach((element) => {
            element.addEventListener("input", new DebounceEvent("input", () => {
                callback(element.value);
                analysis.refresh();
            }, 500).bindTo(element));
        });
    }
}
