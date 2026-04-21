import Strings from "@yoast/yoast-seo-for-typo3/helpers/strings.js";
class Store {
    constructor() {
        this.state = {
            siteName: "",
            focusKeyphrase: null,
            error: null,
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
            slug: "",
            ...this.state.content,
        };
        const updatedContent = { ...defaultContent, ...content };
        updatedContent.title = Strings.stripHtmlTags(updatedContent.title);
        updatedContent.metadata = {
            ...updatedContent.metadata,
            description: Strings.stripHtmlTags(updatedContent.metadata.description ?? ""),
        };
        this.setState({ content: updatedContent });
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
