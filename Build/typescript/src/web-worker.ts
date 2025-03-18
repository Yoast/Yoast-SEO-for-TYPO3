import YoastConfiguration from "@yoast/yoast-seo-for-typo3/yoast-configuration.js";

class WebWorker {
  private static instance: WebWorker;
  private static requestId: number = 0;
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

  public send(type: string, payload: object): Promise<any> {
    console.log(type);
    WebWorker.requestId++;
    const id = WebWorker.requestId;
    const promise = new Promise((resolve, reject) => {
      const listener = (event: any) => {
        if (event.data.id === id) {
          this.worker?.removeEventListener('message', listener);
          if (event.data.error) {
            reject(event.data.error);
          } else {
            resolve(event.data.payload);
          }
        }
      };
      this.worker?.addEventListener('message', listener);
    });
    if (this.worker !== null) {
      this.worker.postMessage({type, id, payload});
    }
    return promise;
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
    this.worker.postMessage({language: locale});
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

const worker = new WebWorker();
export default worker;
