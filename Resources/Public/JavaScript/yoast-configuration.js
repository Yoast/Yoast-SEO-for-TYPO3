class YoastConfigurationManager {
    constructor() {
        this.fallbackLanguage = "en";
        this.urls = null;
        this.cornerstone = false;
        this.focusKeyphrase = null;
        this.TCA = false;
        this.analysisEnabled = true;
        this.data = null;
        this.fieldSelectors = null;
        this.supportedLanguages = null;
        this.inclusiveLanguageEnabled = false;
        this.relatedKeyphrases = null;
    }
    static getInstance() {
        if (!YoastConfigurationManager.instance) {
            YoastConfigurationManager.instance = new YoastConfigurationManager();
        }
        return YoastConfigurationManager.instance;
    }
    getUrl(key) {
        if (!this.urls) {
            return null;
        }
        return this.urls[key];
    }
    setUrl(key, url) {
        if (!this.urls) {
            this.urls = {};
        }
        this.urls[key] = url;
    }
    getFieldSelector(fieldName) {
        if (!this.fieldSelectors) {
            return null;
        }
        return this.fieldSelectors[fieldName];
    }
    getFieldSelectors() {
        return this.fieldSelectors;
    }
    setFieldSelector(fieldName, selector) {
        if (!this.fieldSelectors) {
            this.fieldSelectors = {};
        }
        this.fieldSelectors[fieldName] = selector;
    }
    isTCA() {
        return this.TCA;
    }
    isAnalysisEnabled() {
        return this.analysisEnabled;
    }
    isCornerstone() {
        return this.cornerstone;
    }
    setCornerstone(isCornerstone) {
        this.cornerstone = isCornerstone;
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
        return this.supportedLanguages.indexOf(languageCode) !== -1
            ? languageCode
            : this.fallbackLanguage;
    }
    setSupportedLanguages(languages) {
        this.supportedLanguages = languages;
    }
    isInclusiveLanguageEnabled() {
        return this.inclusiveLanguageEnabled;
    }
    getFocusKeyphrase() {
        return this.focusKeyphrase;
    }
    setFocusKeyword(keyword) {
        if (!this.focusKeyphrase) {
            this.focusKeyphrase = { keyword: null, synonyms: null };
        }
        this.focusKeyphrase.keyword = keyword;
    }
    setFocusKeywordSynonyms(synonyms) {
        if (!this.focusKeyphrase) {
            this.focusKeyphrase = { keyword: null, synonyms: null };
        }
        this.focusKeyphrase.synonyms = synonyms;
    }
    getRelatedKeyphrases() {
        return this.relatedKeyphrases;
    }
    setFromInitialization(configuration) {
        const keys = [
            "urls",
            "analysisEnabled",
            "isCornerstoneContent",
            "focusKeyphrase",
            "TCA",
            "data",
            "fieldSelectors",
            "supportedLanguages",
            "inclusiveLanguageEnabled",
            "relatedKeyphrases",
        ];
        for (const key of keys) {
            if (configuration[key] !== undefined) {
                switch (key) {
                    case "analysisEnabled":
                        this.analysisEnabled = configuration.analysisEnabled ?? true;
                        break;
                    case "isCornerstoneContent":
                        this.cornerstone = configuration.isCornerstoneContent;
                        break;
                    case "TCA":
                        this.TCA = configuration.TCA;
                        break;
                    case "fieldSelectors":
                        configuration.fieldSelectors &&
                            Object.entries(configuration.fieldSelectors).forEach(([fieldName, selector]) => {
                                this.setFieldSelector(fieldName, selector);
                            });
                        break;
                    default:
                        // @ts-ignore
                        this[key] = configuration[key];
                }
            }
        }
    }
}
const YoastConfiguration = YoastConfigurationManager.getInstance();
export default YoastConfiguration;
