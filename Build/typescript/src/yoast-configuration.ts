class YoastConfiguration {
  private static configuration: YoastConfig | null = null;
  private static fallbackLanguage: string = 'en';

  static get(key: string): string | string[] | object | null {
    if (!YoastConfiguration.configuration) {
      YoastConfiguration.configuration = window.YoastConfig;
    }
    return (YoastConfiguration.configuration as Record<string, any>)?.[key] ?? null;
  }

  static getUrl(key: string): string | null {
    const urls = YoastConfiguration.get('urls') as Record<string, string>;
    if (urls && urls[key]) {
      return urls[key];
    }
    return null;
  }

  static getFieldSelector(fieldName: string): string | null {
    const fieldSelectors = YoastConfiguration.get('fieldSelectors') as Record<string, string>;
    if (fieldSelectors && fieldSelectors[fieldName]) {
      return fieldSelectors[fieldName];
    }
    return null;
  }

  static getSupportedLanguage(locale: string): string {
    const supportedLanguages = YoastConfiguration.get('supportedLanguages') as string[];
    if (!supportedLanguages) {
      return YoastConfiguration.fallbackLanguage;
    }

    const languageCode = locale.slice(0, 2);
    return supportedLanguages.indexOf(languageCode) !== -1 ? languageCode : YoastConfiguration.fallbackLanguage;
  }
}

export default YoastConfiguration;
