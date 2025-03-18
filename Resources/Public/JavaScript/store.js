import Strings from "@yoast/yoast-seo-for-typo3/helpers/strings.js";
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
    updateContent(content) {
        const defaultContent = {
            url: "",
            title: "",
            body: "",
            metadata: { description: "" },
            titleConfiguration: { prepend: "", append: "" },
            locale: "",
            favicon: "",
            ...this.state.content,
        };
        this.state.content = { ...defaultContent, ...content };
        this.state.content.title = Strings.stripHtmlTags(this.state.content.title);
        this.state.content.metadata.description = Strings.stripHtmlTags(this.state.content.metadata.description || "");
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
