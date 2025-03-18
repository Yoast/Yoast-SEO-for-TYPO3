class YoastConfiguration {
    static get(key) {
        if (!YoastConfiguration.configuration) {
            YoastConfiguration.configuration = window.YoastConfig;
        }
        return YoastConfiguration.configuration?.[key] ?? null;
    }
    static getUrl(key) {
        const urls = YoastConfiguration.get('urls');
        if (urls && urls[key]) {
            return urls[key];
        }
        return null;
    }
    static getFieldSelector(fieldName) {
        const fieldSelectors = YoastConfiguration.get('fieldSelectors');
        if (fieldSelectors && fieldSelectors[fieldName]) {
            return fieldSelectors[fieldName];
        }
        return null;
    }
    static getSupportedLanguage(locale) {
        const supportedLanguages = YoastConfiguration.get('supportedLanguages');
        if (!supportedLanguages) {
            return YoastConfiguration.fallbackLanguage;
        }
        const languageCode = locale.slice(0, 2);
        return supportedLanguages.indexOf(languageCode) !== -1 ? languageCode : YoastConfiguration.fallbackLanguage;
    }
}
YoastConfiguration.configuration = null;
YoastConfiguration.fallbackLanguage = 'en';
export default YoastConfiguration;
