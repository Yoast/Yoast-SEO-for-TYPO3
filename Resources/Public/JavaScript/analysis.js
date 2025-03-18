import { store } from "@yoast/yoast-seo-for-typo3/store.js";
import Paper from "@yoast/yoast-seo-for-typo3/paper.js";
import measureTextWidth from "@yoast/yoast-seo-for-typo3/helpers/text-width.js";
import worker from "@yoast/yoast-seo-for-typo3/web-worker.js";
class Analysis {
    constructor() { }
    static getInstance() {
        if (!Analysis.instance) {
            Analysis.instance = new Analysis();
        }
        return Analysis.instance;
    }
    refresh() {
        const state = store.getState();
        const paper = new Paper(state.body, {
            keyword: state.focusKeyword,
            title: state.title,
            synonyms: state.synonyms,
            description: state.description,
            locale: state.locale,
            titleWidth: measureTextWidth(state.titleWidth),
        });
        console.log("Paper", paper.serialize());
        worker.set(state.cornerstone, state.locale);
        worker.send('analyze', {
            id: 'test',
            action: 'analyze',
            payload: { paper: paper.serialize() }
        }).then((response) => {
            console.log(response);
        });
    }
}
export const analysis = Analysis.getInstance();
