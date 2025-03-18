export default class Result {
  public result: object;
  public data: object;
  constructor( result: object, data: object = {} ) {
    this.result = result;
    this.data = data;
  }
}
