import {YoastAnalysis, YoastContent, YoastKeyphrase } from "."

export type State = {
  siteName: string
  focusKeyphrase: YoastKeyphrase | null
  content?: YoastContent
  analysis?: YoastAnalysis
  error?: boolean
}
