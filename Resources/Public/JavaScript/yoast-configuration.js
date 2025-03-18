class YoastConfigurationManager {
    constructor() {
        this.fallbackLanguage = 'en';
        this.urls = null;
        this.cornerstone = false;
        this.focusKeyphrase = null;
        this.TCA = false;
        this.data = null;
        this.fieldSelectors = null;
        this.supportedLanguages = null;
        this.relatedKeyphrases = null;
        if (!YoastConfigurationManager.instance) {
            YoastConfigurationManager.instance = this;
        }
        return YoastConfigurationManager.instance;
    }
    getUrl(key) {
        if (!this.urls) {
            return null;
        }
        return this.urls[key];
    }
    getFieldSelector(fieldName) {
        if (!this.fieldSelectors) {
            return null;
        }
        return this.fieldSelectors[fieldName];
    }
    isTCA() {
        return this.TCA;
    }
    isCornerstone() {
        return this.cornerstone;
    }
    getData(dataKey) {
        if (!this.data) {
            return null;
        }
        return this.data[dataKey];
    }
    getSupportedLanguage(locale) {
        if (!this.supportedLanguages) {
            return this.fallbackLanguage;
        }
        const languageCode = locale.slice(0, 2);
        return this.supportedLanguages.indexOf(languageCode) !== -1 ? languageCode : this.fallbackLanguage;
    }
    setFromInitialization(configuration) {
        if (configuration.urls) {
            this.urls = configuration.urls;
        }
        if (configuration.isCornerstoneContent) {
            this.cornerstone = configuration.isCornerstoneContent;
        }
        if (configuration.focusKeyphrase) {
            this.focusKeyphrase = configuration.focusKeyphrase;
        }
        if (configuration.TCA) {
            this.TCA = configuration.TCA;
        }
        if (configuration.data) {
            this.data = configuration.data;
        }
        if (configuration.fieldSelectors) {
            this.fieldSelectors = configuration.fieldSelectors;
        }
        if (configuration.supportedLanguages) {
            this.supportedLanguages = configuration.supportedLanguages;
        }
        if (configuration.relatedKeyphrases) {
            this.relatedKeyphrases = configuration.relatedKeyphrases;
        }
    }
}
const YoastConfiguration = new YoastConfigurationManager();
export default YoastConfiguration;
