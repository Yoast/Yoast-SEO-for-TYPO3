import YoastConfiguration from "@yoast/yoast-seo-for-typo3/yoast-configuration.js";
import Request from "@yoast/yoast-seo-for-typo3/request/request.js";
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
    sendRequest(action, payload, data = {}) {
        const id = this.createRequestId();
        const promise = this.createRequestPromise(id, data);
        this.send(action, id, payload);
        return promise;
    }
    send(type, id, payload) {
        if (this.worker !== null) {
            this.worker.postMessage({ type: type, id: id, payload: payload });
        }
    }
    set(cornerstone, locale) {
        if (this.worker !== null) {
            this.worker.terminate();
        }
        this.initializeWorker(cornerstone, YoastConfiguration.getSupportedLanguage(locale));
    }
    initializeWorker(cornerstone, locale) {
        this.worker = new Worker(YoastConfiguration.getUrl('workerUrl'));
        this.worker.onmessage = this.handleMessage;
        this.worker.postMessage({ language: locale });
        this.sendRequest('initialize', {
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
    handleMessage({ data: { type, id, payload } }) {
        const request = WebWorker.requests[id];
        if (!request) {
            console.warn("yoast-seo unknown webworker request:", payload);
            return;
        }
        switch (type) {
            case "initialize:done":
            case "loadScript:done":
            case "customMessage:done":
            case "runResearch:done":
            case "analyzeRelatedKeywords:done":
            case "analyze:done":
                request.resolve(payload);
                break;
            case "analyze:failed":
            case "loadScript:failed":
            case "customMessage:failed":
            case "runResearch:failed":
            case "analyzeRelatedKeywords:failed":
                request.reject(payload);
                break;
            default:
                console.warn("yoast-seo unknown webworker action:", payload);
        }
        delete WebWorker.requests[id];
    }
    createRequestId() {
        WebWorker.requestId++;
        return WebWorker.requestId;
    }
    createRequestPromise(id, data = {}) {
        return new Promise((resolve, reject) => {
            WebWorker.requests[id] = new Request(resolve, reject, data);
        });
    }
}
WebWorker.requestId = 0;
WebWorker.requests = {};
const worker = new WebWorker();
export default worker;
