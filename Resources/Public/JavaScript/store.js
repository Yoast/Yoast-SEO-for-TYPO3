class Store {
    constructor() {
        this.state = {
            siteName: "",
            focusKeyphrase: null,
            error: false,
        };
        this.listeners = new Set();
    }
    static getInstance() {
        if (!Store.instance) {
            Store.instance = new Store();
        }
        return Store.instance;
    }
    getState() {
        return this.state;
    }
    setState(newState) {
        this.state = { ...this.state, ...newState };
        this.notify();
    }
    subscribe(callback) {
        this.listeners.add(callback);
        return () => this.listeners.delete(callback);
    }
    notify() {
        this.listeners.forEach((callback) => callback(this.state));
    }
}
const store = Store.getInstance();
export default store;
