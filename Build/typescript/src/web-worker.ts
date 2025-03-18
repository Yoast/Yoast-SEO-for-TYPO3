import Request from "@yoast/yoast-seo-for-typo3/request/request.js"
import Result from "@yoast/yoast-seo-for-typo3/request/result.js"
import YoastConfiguration from "@yoast/yoast-seo-for-typo3/yoast-configuration.js"

type WebworkerData = {
  type: string
  id: number
  payload: object
}

class WebWorker {
  private static instance: WebWorker
  private requestId: number = 0
  private requests: Record<number, Request> = {}
  private worker: Worker | null = null

  private constructor() {}

  public static getInstance() {
    if (!WebWorker.instance) {
      this.instance = new WebWorker()
    }
    return WebWorker.instance
  }

  public get(): Worker | null {
    return this.worker
  }

  public sendRequest<T>(
    action: string,
    payload: object,
    data = {}
  ): Promise<Result<T>> {
    const id = this.createRequestId()
    const promise = this.createRequestPromise(id, data)
    this.send(action, id, payload)
    return promise
  }

  private send(type: string, id: number, payload: object) {
    if (this.worker !== null) {
      this.worker.postMessage({ type: type, id: id, payload: payload })
    }
  }

  public async set(cornerstone: boolean, locale: string): Promise<void> {
    if (this.worker !== null) {
      this.worker.terminate()
    }
    await this.initializeWorker(
      cornerstone,
      YoastConfiguration.getSupportedLanguage(locale)
    )
  }

  private async initializeWorker(
    cornerstone: boolean,
    locale: string
  ): Promise<void> {
    this.worker = new Worker(YoastConfiguration.getUrl("workerUrl") as string)
    this.worker.postMessage({ language: locale })

    this.handleMessage = this.handleMessage.bind(this)
    this.worker.onmessage = this.handleMessage

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
    })
  }

  handleMessage({ data: { type, id, payload } }: { data: WebworkerData }) {
    const request = this.requests.hasOwnProperty(id) ? this.requests[id] : null
    if (!request) {
      console.warn("yoast-seo unknown webworker request:", payload)
      return
    }

    if (type.endsWith(":done")) {
      request.resolve(payload)
    } else if (type.endsWith(":failed")) {
      request.reject(payload)
    } else {
      console.warn("yoast-seo unknown webworker action:", payload)
    }

    delete this.requests[id]
  }

  private createRequestId(): number {
    this.requestId++
    return this.requestId
  }

  private createRequestPromise(id: number, data = {}): Promise<any> {
    return new Promise((resolve, reject) => {
      this.requests[id] = new Request(resolve, reject, data)
    })
  }
}

const worker = WebWorker.getInstance()
export default worker
