import YoastConfiguration from "@yoast/yoast-seo-for-typo3/yoast-configuration.js";
class FormEngine {
    static getElements(fieldName) {
        const selector = YoastConfiguration.getFieldSelector(fieldName);
        if (selector) {
            return document.querySelectorAll(`[data-formengine-input-name="${selector}"]`);
        }
        return null;
    }
    static getElement(fieldName) {
        const selector = YoastConfiguration.getFieldSelector(fieldName);
        if (selector) {
            return document.querySelector(`[data-formengine-input-name="${selector}"]`);
        }
        return null;
    }
    static getSelectElement(fieldName) {
        const selector = YoastConfiguration.getFieldSelector(fieldName);
        if (selector) {
            return document.querySelector(`[name="${selector}"]`);
        }
        return null;
    }
    static getInputElementValue(fieldName) {
        const element = this.getElement(fieldName);
        return element?.value ?? "";
    }
    static getSelectElementValue(fieldName) {
        const element = this.getSelectElement(fieldName);
        return element?.value ?? "";
    }
}
export default FormEngine;
