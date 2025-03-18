import { YoastAnalysis, YoastContent, YoastKeyphrase } from "."
import { RequestError } from "./request-error"

export type State = {
  siteName: string
  focusKeyphrase: YoastKeyphrase | null
  content?: YoastContent
  analysis?: YoastAnalysis
  error?: RequestError["error"] | false
}
