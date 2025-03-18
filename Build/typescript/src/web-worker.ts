import YoastConfiguration from "@yoast/yoast-seo-for-typo3/yoast-configuration.js";
import Request from "@yoast/yoast-seo-for-typo3/request/request.js";
import Result from "@yoast/yoast-seo-for-typo3/request/result";

type WebworkerData = {
  type: string
  id: number
  payload: object
}

class WebWorker {
  private static instance: WebWorker;
  private static requestId: number = 0;
  private static requests: Record<number, Request> = {};
  private worker: Worker | null = null;

  constructor() {
    if (!WebWorker.instance) {
      WebWorker.instance = this;
    }
    return WebWorker.instance;
  }

  public get(): Worker | null {
    return this.worker;
  }

  public sendRequest(action: string, payload: object, data = {}): Promise<Result> {
    const id = this.createRequestId();
    const promise = this.createRequestPromise(id, data);
    this.send(action, id, payload);
    return promise;
  }

  private send(type: string, id: number, payload: object) {
    if (this.worker !== null) {
      this.worker.postMessage({type: type, id: id, payload: payload});
    }
  }

  public set(cornerstone: boolean, locale: string): void {
    if (this.worker !== null) {
      this.worker.terminate();
    }
    this.initializeWorker(
      cornerstone,
      YoastConfiguration.getSupportedLanguage(locale)
    )
  }

  private initializeWorker(cornerstone: boolean, locale: string): void {
    this.worker = new Worker(YoastConfiguration.getUrl('workerUrl') as string);
    this.worker.onmessage = this.handleMessage;
    this.worker.postMessage({language: locale});
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

  handleMessage({data: {type, id, payload}}: { data: WebworkerData }) {
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

  private createRequestId(): number {
    WebWorker.requestId++;
    return WebWorker.requestId;
  }

  private createRequestPromise(id: number, data = {}): Promise<any> {
    return new Promise((resolve, reject) => {
      WebWorker.requests[id] = new Request(resolve, reject, data);
    });
  }
}

const worker = new WebWorker();
export default worker;
