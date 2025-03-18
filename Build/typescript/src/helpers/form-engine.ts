import { YoastFields } from "@yoast/yoast-seo-for-typo3/types/yoast"
import YoastConfiguration from "@yoast/yoast-seo-for-typo3/yoast-configuration.js"

class FormEngine {
  public static getElements(
    fieldName: keyof YoastFields
  ): NodeListOf<HTMLElement> | null {
    const selector = YoastConfiguration.getFieldSelector(fieldName)
    if (selector) {
      return document.querySelectorAll(
        `[data-formengine-input-name="${selector}"]`
      )
    }
    return null
  }

  public static getElement(fieldName: keyof YoastFields): HTMLElement | null {
    const selector = YoastConfiguration.getFieldSelector(fieldName)
    if (selector) {
      return document.querySelector(
        `[data-formengine-input-name="${selector}"]`
      )
    }
    return null
  }

  public static getSelectElement(
    fieldName: keyof YoastFields
  ): HTMLSelectElement | null {
    const selector = YoastConfiguration.getFieldSelector(fieldName)
    if (selector) {
      return document.querySelector(`[name="${selector}"]`) as HTMLSelectElement
    }
    return null
  }

  public static getInputElementValue(fieldName: keyof YoastFields): string {
    const element = this.getElement(fieldName) as HTMLInputElement
    return element?.value ?? ""
  }

  public static getSelectElementValue(fieldName: keyof YoastFields): string {
    const element = this.getSelectElement(fieldName)
    return element?.value ?? ""
  }
}

export default FormEngine
