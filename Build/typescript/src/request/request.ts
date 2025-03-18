import Result from "@yoast/yoast-seo-for-typo3/request/result.js";

export default class Request {
  private readonly _resolve: any;
  private readonly _reject: any;
  private readonly _data: {};
  constructor( resolve: any, reject: any, data = {} ) {
    this._resolve = resolve;
    this._reject = reject;
    this._data = data;
  }

  resolve( payload: object = {}): void {
    const result = new Result( payload, this._data );
    this._resolve( result );
  }

  reject( payload = {} ) {
    const result = new Result( payload, this._data );
    this._reject( result );
  }
}
