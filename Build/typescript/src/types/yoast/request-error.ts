export type RequestError = {
  error: {
    reason: string
    url: string
    statusCode: number
  }
}
