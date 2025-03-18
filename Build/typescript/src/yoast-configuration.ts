class YoastConfigurationManager {
    private static instance: YoastConfigurationManager;
    private fallbackLanguage: string = 'en';

    private urls: YoastUrls | null = null;
    private cornerstone: boolean = false;
    private focusKeyphrase: YoastKeyphrase | null = null;
    private TCA: boolean = false;
    private data: YoastData | null = null;
    private fieldSelectors: YoastFields | null = null;
    private supportedLanguages: string[] | null = null;
    private relatedKeyphrases: Record<string, YoastKeyphrase> | null = null;

    constructor() {
        if (!YoastConfigurationManager.instance) {
            YoastConfigurationManager.instance = this;
        }
        return YoastConfigurationManager.instance;
    }

    getUrl(key: keyof YoastUrls): string | null {
        if (!this.urls) {
            return null;
        }
        return this.urls[key];
    }

    getFieldSelector(fieldName: keyof YoastFields): string | null {
        if (!this.fieldSelectors) {
            return null;
        }
        return this.fieldSelectors[fieldName];
    }

    isTCA(): boolean {
        return this.TCA;
    }

    isCornerstone(): boolean {
        return this.cornerstone;
    }

    getData(dataKey: keyof YoastData): number | string | null {
        if (!this.data) {
            return null;
        }
        return this.data[dataKey];
    }

    getSupportedLanguage(locale: string): string {
        if (!this.supportedLanguages) {
            return this.fallbackLanguage;
        }

        const languageCode = locale.slice(0, 2);
        return this.supportedLanguages.indexOf(languageCode) !== -1 ? languageCode : this.fallbackLanguage;
    }

    setFromInitialization(configuration: YoastConfig): void {
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
