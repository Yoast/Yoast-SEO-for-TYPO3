import {YoastConfig} from "@yoast/yoast-seo-for-typo3/types/yoast";

export {};

declare global {
  interface Window {
    YoastTranslations: YoastConfig
  }
}
