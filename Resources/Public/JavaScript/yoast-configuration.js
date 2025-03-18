class YoastConfigurationManager {
    constructor() {
        this.fallbackLanguage = "en";
        this.urls = null;
        this.cornerstone = false;
        this.focusKeyphrase = null;
        this.TCA = false;
        this.data = null;
        this.fieldSelectors = null;
        this.supportedLanguages = null;
        this.relatedKeyphrases = null;
    }
    static getInstance() {
        if (!YoastConfigurationManager.instance) {
            this.instance = new YoastConfigurationManager();
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
        return this.supportedLanguages.indexOf(languageCode) !== -1
            ? languageCode
            : this.fallbackLanguage;
    }
    getFocusKeyphrase() {
        return this.focusKeyphrase;
    }
    getRelatedKeyphrases() {
        return this.relatedKeyphrases;
    }
    setFromInitialization(configuration) {
        const keys = [
            "urls",
            "isCornerstoneContent",
            "focusKeyphrase",
            "TCA",
            "data",
            "fieldSelectors",
            "supportedLanguages",
            "relatedKeyphrases",
        ];
        for (const key of keys) {
            if (configuration[key] !== undefined) {
                switch (key) {
                    case "isCornerstoneContent":
                        this.cornerstone = configuration.isCornerstoneContent;
                        break;
                    case "TCA":
                        this.TCA = configuration.TCA;
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
