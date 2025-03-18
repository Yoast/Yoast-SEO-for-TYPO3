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
