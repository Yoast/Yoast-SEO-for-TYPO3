export class AjaxResponse {
    constructor(response) {
        this.response = response;
    }
    async resolve(expectedType) {
        if (typeof this.resolvedBody !== "undefined") {
            return this.resolvedBody;
        }
        const contentType = this.response.headers.get("Content-Type") ?? "";
        if (expectedType === "json" || contentType.startsWith("application/json")) {
            this.resolvedBody = await this.response.json();
        }
        else {
            this.resolvedBody = await this.response.text();
        }
        return this.resolvedBody;
    }
    raw() {
        return this.response;
    }
}
