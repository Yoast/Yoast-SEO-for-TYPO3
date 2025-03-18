import Strings from "@yoast/yoast-seo-for-typo3/helpers/strings.js"
import { State } from "@yoast/yoast-seo-for-typo3/types/yoast"

type Listener = (state: State) => void

class Store {
  private static instance: Store
  private state: State = {
    siteName: "",
    focusKeyphrase: null,
    error: false,
  }
  private listeners: Set<Listener> = new Set()

  private constructor() {}

  public static getInstance(): Store {
    if (!Store.instance) {
      Store.instance = new Store()
    }
    return Store.instance
  }

  public getState(): State {
    return this.state
  }

  public setState(newState: Partial<State>): void {
    this.state = { ...this.state, ...newState }
    this.notify()
  }

  public updateContent(content: Partial<State["content"]>): void {
    const defaultContent: State["content"] = {
      url: "",
      title: "",
      body: "",
      metadata: { description: "" },
      titleConfiguration: { prepend: "", append: "" },
      locale: "",
      favicon: "",
      ...this.state.content,
    }
    this.state.content = { ...defaultContent, ...content }
    this.state.content.title = Strings.stripHtmlTags(this.state.content.title)
    this.state.content.metadata.description = Strings.stripHtmlTags(
      this.state.content.metadata.description || ""
    )
    this.notify()
  }

  public subscribe(callback: Listener): () => void {
    this.listeners.add(callback)
    return () => this.listeners.delete(callback)
  }

  private notify(): void {
    this.listeners.forEach((callback) => callback(this.state))
  }
}

const store = Store.getInstance()
export default store
