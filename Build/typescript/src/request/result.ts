export default class Result<T> {
  public result: T;
  public data: object;
  constructor( result: T, data: object) {
    this.result = result;
    this.data = data;
  }
}
