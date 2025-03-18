type State = Record<string, any>;
type Listener = (state: State) => void;

class Store {
  private static instance: Store;
  private state: State = {};
  private listeners: Set<Listener> = new Set();

  private constructor() {}

  public static getInstance(): Store {
    if (!Store.instance) {
      Store.instance = new Store();
    }
    return Store.instance;
  }

  public getState(): State {
    return this.state;
  }

  public setState(newState: Partial<State>): void {
    this.state = { ...this.state, ...newState };
    this.notify();
  }

  public subscribe(callback: Listener): () => void {
    this.listeners.add(callback);
    return () => this.listeners.delete(callback);
  }

  private notify(): void {
    this.listeners.forEach(callback => callback(this.state));
  }
}

export const store = Store.getInstance();
