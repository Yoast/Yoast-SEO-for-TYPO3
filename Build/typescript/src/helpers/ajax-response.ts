export class AjaxResponse {
  public readonly response: Response
  private resolvedBody: string | any

  constructor(response: Response) {
    this.response = response
  }

  public async resolve<T>(expectedType?: string): Promise<string | any> {
    if (typeof this.resolvedBody !== "undefined") {
      return this.resolvedBody
    }
    const contentType: string = this.response.headers.get("Content-Type") ?? ""
    if (expectedType === "json" || contentType.startsWith("application/json")) {
      this.resolvedBody = await this.response.json()
    } else {
      this.resolvedBody = await this.response.text()
    }
    return this.resolvedBody as T
  }

  public raw(): Response {
    return this.response
  }
}
