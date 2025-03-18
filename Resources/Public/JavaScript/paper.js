export default class Paper {
    constructor(text, attributes) {
        this._text = text || "";
        this._attributes = attributes;
    }
    serialize() {
        return {
            _parseClass: "Paper",
            text: this._text,
            ...this._attributes,
        };
    }
}
