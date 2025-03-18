import analysis from "@yoast/yoast-seo-for-typo3/analysis.js";
import { setAttributes } from "@yoast/yoast-seo-for-typo3/helpers/attributes.js";
import store from "@yoast/yoast-seo-for-typo3/store.js";
class Insights {
    constructor() {
        this.cachedAnalysis = null;
        store.subscribe((state) => {
            if (!state.content || !state.analysis)
                return;
            const analysis = JSON.stringify(state.analysis);
            if (this.cachedAnalysis !== analysis) {
                this.cachedAnalysis = analysis;
                this.updateInsights();
                this.updateFleschReadingScore();
                this.updateReadingTime();
                this.updateWordCount();
            }
        });
    }
    async updateInsights() {
        const insightsComponent = document.querySelector("yoast-insights");
        if (!insightsComponent)
            return;
        try {
            const response = await analysis.runResearch("getProminentWordsForInsights");
            insightsComponent.setAttribute("keywords", this.getKeywords(response.result));
        }
        catch (error) {
            console.error("Error fetching prominent words for insights:", error);
        }
    }
    getKeywords(keywords) {
        return JSON.stringify(keywords.map((item) => ({
            word: item.word,
            occurrences: item.occurrences,
        })));
    }
    async updateFleschReadingScore() {
        const fleschComponent = document.querySelector("yoast-flesch-reading-score");
        if (!fleschComponent)
            return;
        try {
            const response = await analysis.runResearch("getFleschReadingScore");
            if (!response.result) {
                fleschComponent.setAttribute("unsupportedLanguage", "true");
            }
            else {
                fleschComponent.removeAttribute("unsupportedLanguage");
                setAttributes(fleschComponent, {
                    score: String(response.result.score),
                    difficulty: String(response.result.difficulty),
                });
            }
        }
        catch (error) {
            console.error("Error fetching Flesch reading score:", error);
        }
    }
    async updateReadingTime() {
        const readingTimeComponent = document.querySelector("yoast-reading-time");
        if (!readingTimeComponent)
            return;
        try {
            const response = await analysis.runResearch("readingTime");
            readingTimeComponent.setAttribute("reading-time", String(response.result));
        }
        catch (error) {
            console.error("Error fetching reading time:", error);
        }
    }
    async updateWordCount() {
        const wordCountComponent = document.querySelector("yoast-word-count");
        if (!wordCountComponent)
            return;
        try {
            const response = await analysis.runResearch("wordCountInText");
            setAttributes(wordCountComponent, {
                count: String(response.result.count),
                unit: response.result.unit,
            });
        }
        catch (error) {
            console.error("Error fetching word count in text:", error);
        }
    }
}
export default new Insights();
