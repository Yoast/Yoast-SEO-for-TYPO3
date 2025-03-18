import YoastConfiguration from "@yoast/yoast-seo-for-typo3/yoast-configuration";

class FormEngineHelper {
  public getElement(fieldName: keyof YoastFields): HTMLElement | null {
    const selector = YoastConfiguration.getFieldSelector(fieldName);
    if (selector) {
      return document.querySelector(`[data-formengine-input-name="${selector}"]`);
    }
    return null;
  }

  public getElements(fieldName: keyof YoastFields): NodeListOf<HTMLElement> | null {
    const selector = YoastConfiguration.getFieldSelector(fieldName);
    if (selector) {
      return document.querySelectorAll(`[data-formengine-input-name="${selector}"]`);
    }
    return null;
  }
}

export default FormEngineHelper;
