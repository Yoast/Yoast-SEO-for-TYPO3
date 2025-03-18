export default class Paper {
  public _text: string;
  public _attributes: PaperAttributes;
  constructor(text: string, attributes: PaperAttributes) {
    this._text = text || "";
    this._attributes = attributes;
  }
  public serialize() {
    return {
      _parseClass: "Paper",
      text: this._text,
      ...this._attributes,
    };
  }
}
