import analysis from "@yoast/yoast-seo-for-typo3/analysis.js";
import FormEngine from "@yoast/yoast-seo-for-typo3/helpers/form-engine.js";
import store from "@yoast/yoast-seo-for-typo3/store.js";
import YoastConfiguration from "@yoast/yoast-seo-for-typo3/yoast-configuration.js";
class Cornerstone {
    constructor() {
        this.initialized = false;
        store.subscribe(() => {
            this.initializeCornerstoneEvent();
        });
    }
    initializeCornerstoneEvent() {
        if (this.initialized)
            return;
        this.initialized = true;
        const cornerstoneField = FormEngine.getElement("cornerstone");
        if (!cornerstoneField)
            return;
        cornerstoneField.addEventListener("change", (event) => {
            const target = event.target;
            const isCornerstone = target.checked;
            YoastConfiguration.setCornerstone(isCornerstone);
            analysis.refresh();
        });
    }
}
export default new Cornerstone();
