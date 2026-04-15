import { YoastFields } from "@yoast/yoast-seo-for-typo3/types/yoast"
import YoastConfiguration from "@yoast/yoast-seo-for-typo3/yoast-configuration.js"

class FormEngine {
  public static getElements<T extends HTMLElement>(
    fieldName: keyof YoastFields
  ): NodeListOf<T> | null {
    const selector = YoastConfiguration.getFieldSelector(fieldName)
    if (selector) {
      return document.querySelectorAll<T>(
        `[data-formengine-input-name="${selector}"]`
      )
    }
    return null
  }

  public static getElement<T extends HTMLElement>(
    fieldName: keyof YoastFields
  ): T | null {
    const selector = YoastConfiguration.getFieldSelector(fieldName)
    if (selector) {
      return document.querySelector<T>(
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
      return document.querySelector<HTMLSelectElement>(`[name="${selector}"]`)
    }
    return null
  }

  public static getInputElementValue(fieldName: keyof YoastFields): string {
    const element = this.getElement<HTMLInputElement>(fieldName)
    return element?.value ?? ""
  }

  public static getSelectElementValue(fieldName: keyof YoastFields): string {
    const element = this.getSelectElement(fieldName)
    return element?.value ?? ""
  }
}

export default FormEngine
