import measureTextWidth from "@yoast/yoast-seo-for-typo3/helpers/text-width.js";
import Paper from "@yoast/yoast-seo-for-typo3/paper.js";
import store from "@yoast/yoast-seo-for-typo3/store.js";
import worker from "@yoast/yoast-seo-for-typo3/web-worker.js";
import YoastConfiguration from "@yoast/yoast-seo-for-typo3/yoast-configuration.js";
class Analysis {
    constructor() { }
    static getInstance() {
        if (!Analysis.instance) {
            Analysis.instance = new Analysis();
        }
        return Analysis.instance;
    }
    async refresh() {
        const state = store.getState();
        if (!state.content)
            return;
        const paper = this.createSerializedPaper(state.content, state.focusKeyphrase);
        await this.configureWorker(state.content);
        try {
            const response = await this.analyzePaper(paper);
            if (this.hasRelatedKeyphrases()) {
                const related = await this.analyzeRelatedKeyphrases(paper);
                response.result.seo = { ...response.result.seo, ...related.result.seo };
            }
            store.setState({ analysis: response.result });
        }
        catch (error) {
            console.error("yoast-seo analysis failed", error);
        }
    }
    async configureWorker(content) {
        await worker.set(YoastConfiguration.isCornerstone(), content.locale);
    }
    async analyzePaper(paper) {
        return await worker.sendRequest("analyze", { paper });
    }
    async runResearch(name, content = null, focusKeyphrase = null) {
        if (content === null) {
            const state = store.getState();
            if (!state.content)
                return Promise.reject("No content to analyze");
            content = state.content;
            focusKeyphrase = focusKeyphrase ?? state.focusKeyphrase;
        }
        const paper = this.createSerializedPaper(content, focusKeyphrase);
        return await worker.sendRequest("runResearch", { name, paper });
    }
    async analyzeRelatedKeyphrases(paper) {
        return await worker.sendRequest("analyzeRelatedKeywords", {
            paper,
            relatedKeywords: YoastConfiguration.getRelatedKeyphrases(),
        });
    }
    hasRelatedKeyphrases() {
        return YoastConfiguration.getRelatedKeyphrases() !== null;
    }
    createContent(content) {
        return {
            url: content.url || "",
            title: content.title || "",
            body: content.body || "",
            metadata: content.metadata || { description: "" },
            titleConfiguration: content.titleConfiguration || {
                prepend: "",
                append: "",
            },
            locale: content.locale || "en_US",
            favicon: content.favicon || "",
        };
    }
    createSerializedPaper(content, focusKeyphrase) {
        const paper = new Paper(content.body, {
            keyword: focusKeyphrase?.keyword ?? "",
            title: content.title,
            synonyms: focusKeyphrase?.synonyms ?? "",
            description: content.metadata.description ?? "",
            locale: content.locale,
            titleWidth: measureTextWidth(content.title),
        });
        return paper.serialize();
    }
}
const analysis = Analysis.getInstance();
export default analysis;
