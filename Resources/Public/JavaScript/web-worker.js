import Request from "@yoast/yoast-seo-for-typo3/request/request.js";
import YoastConfiguration from "@yoast/yoast-seo-for-typo3/yoast-configuration.js";
class WebWorker {
    constructor() {
        this.requestId = 0;
        this.requests = {};
        this.worker = null;
    }
    static getInstance() {
        if (!WebWorker.instance) {
            this.instance = new WebWorker();
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
    async set(cornerstone, locale) {
        if (this.worker !== null) {
            this.worker.terminate();
        }
        await this.initializeWorker(cornerstone, YoastConfiguration.getSupportedLanguage(locale));
    }
    async initializeWorker(cornerstone, locale) {
        this.worker = new Worker(YoastConfiguration.getUrl("workerUrl"));
        this.worker.postMessage({ language: locale });
        this.handleMessage = this.handleMessage.bind(this);
        this.worker.onmessage = this.handleMessage;
        await this.sendRequest("initialize", {
            locale: locale,
            contentAnalysisActive: true,
            keywordAnalysisActive: true,
            inclusiveLanguageAnalysisActive: true,
            useKeywordDistribution: true,
            useCornerstone: cornerstone,
            logLevel: "ERROR",
            translations: [], //YoastConfiguration.get('translations'),
            defaultQueryParams: null,
        });
    }
    handleMessage({ data: { type, id, payload } }) {
        const request = this.requests.hasOwnProperty(id) ? this.requests[id] : null;
        if (!request) {
            console.warn("yoast-seo unknown webworker request:", payload);
            return;
        }
        if (type.endsWith(":done")) {
            request.resolve(payload);
        }
        else if (type.endsWith(":failed")) {
            request.reject(payload);
        }
        else {
            console.warn("yoast-seo unknown webworker action:", payload);
        }
        delete this.requests[id];
    }
    createRequestId() {
        this.requestId++;
        return this.requestId;
    }
    createRequestPromise(id, data = {}) {
        return new Promise((resolve, reject) => {
            this.requests[id] = new Request(resolve, reject, data);
        });
    }
}
const worker = WebWorker.getInstance();
export default worker;
