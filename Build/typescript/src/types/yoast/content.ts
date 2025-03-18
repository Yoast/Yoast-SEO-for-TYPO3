export type YoastContent = {
  url: string
  title: string
  body: string
  metadata: {
    description?: string
  }
  titleConfiguration: {
    prepend: string
    append: string
  }
  locale: string
  cornerstone: boolean
  favicon: string
}
