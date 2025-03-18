import YoastConfiguration from "@yoast/yoast-seo-for-typo3/yoast-configuration.js";
class WebWorker {
    constructor() {
        this.worker = null;
        if (!WebWorker.instance) {
            WebWorker.instance = this;
        }
        return WebWorker.instance;
    }
    get() {
        return this.worker;
    }
    send(type, payload) {
        console.log(type);
        WebWorker.requestId++;
        const id = WebWorker.requestId;
        const promise = new Promise((resolve, reject) => {
            const listener = (event) => {
                if (event.data.id === id) {
                    this.worker?.removeEventListener('message', listener);
                    if (event.data.error) {
                        reject(event.data.error);
                    }
                    else {
                        resolve(event.data.payload);
                    }
                }
            };
            this.worker?.addEventListener('message', listener);
        });
        if (this.worker !== null) {
            this.worker.postMessage({ type, id, payload });
        }
        return promise;
    }
    set(cornerstone, locale) {
        if (this.worker !== null) {
            this.worker.terminate();
        }
        this.initializeWorker(cornerstone, YoastConfiguration.getSupportedLanguage(locale));
    }
    initializeWorker(cornerstone, locale) {
        this.worker = new Worker(YoastConfiguration.getUrl('workerUrl'));
        this.worker.postMessage({ language: locale });
        this.send('initialize', {
            locale: locale,
            contentAnalysisActive: true,
            keywordAnalysisActive: true,
            useKeywordDistribution: true,
            useCornerstone: cornerstone,
            logLevel: "ERROR",
            translations: YoastConfiguration.get('translations'),
            defaultQueryParams: null
        });
    }
}
WebWorker.requestId = 0;
const worker = new WebWorker();
export default worker;
