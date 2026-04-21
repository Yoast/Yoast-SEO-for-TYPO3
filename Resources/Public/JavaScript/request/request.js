import Result from "@yoast/yoast-seo-for-typo3/request/result.js";
export default class Request {
    constructor(resolve, reject, data = {}) {
        this._resolve = resolve;
        this._reject = reject;
        this._data = data;
    }
    resolve(payload = {}) {
        const result = new Result(payload, this._data);
        this._resolve(result);
    }
    reject(payload = {}) {
        const result = new Result(payload, this._data);
        this._reject(result);
    }
}
