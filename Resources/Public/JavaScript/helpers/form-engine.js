import YoastConfiguration from "@yoast/yoast-seo-for-typo3/yoast-configuration";
class FormEngine {
    getElement(fieldName) {
        const selector = YoastConfiguration.getFieldSelector(fieldName);
        if (selector) {
            return document.querySelector(`[data-formengine-input-name="${selector}"]`);
        }
        return null;
    }
    getElements(fieldName) {
        const selector = YoastConfiguration.getFieldSelector(fieldName);
        if (selector) {
            return document.querySelectorAll(`[data-formengine-input-name="${selector}"]`);
        }
        return null;
    }
}
export default FormEngine;
