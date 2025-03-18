import FrontendRequest from "@maxserv/frontend-request/frontend-request.js";
import AjaxRequest from "@typo3/core/ajax/ajax-request.js";
import analysis from "@yoast/yoast-seo-for-typo3/analysis.js";
import { saveProminentWords } from "@yoast/yoast-seo-for-typo3/helpers/prominent-words.js";
import worker from "@yoast/yoast-seo-for-typo3/web-worker.js";
import YoastConfiguration from "@yoast/yoast-seo-for-typo3/yoast-configuration.js";
class Crawler {
    constructor() {
        this.configuration = null;
        this.progressCache = {};
    }
    initialize(configuration) {
        this.configuration = configuration;
        YoastConfiguration.setUrl("workerUrl", configuration.urls.workerUrl);
        YoastConfiguration.setSupportedLanguages(configuration.supportedLanguages);
        this.attachIndexButtonHandlers();
    }
    attachIndexButtonHandlers() {
        const indexButtons = document.querySelectorAll(".js-crawler-index");
        indexButtons.forEach((button) => {
            button.addEventListener("click", () => {
                const site = button.dataset.site || "";
                const language = button.dataset.language || "";
                this.handleStartIndex(site, language);
            });
        });
    }
    handleStartIndex(site, language) {
        this.toggleButtons(true);
        this.removeSavedProgress(site, language);
        const progressDiv = this.getProgressContainer(site, language);
        if (!progressDiv)
            return;
        const progressInfo = progressDiv.querySelector(".js-crawler-pages-progress");
        const progressSuccess = progressDiv.querySelector(".js-crawler-pages-success");
        if (!progressInfo || !progressSuccess)
            return;
        progressDiv.classList.remove("hide");
        new AjaxRequest(this.configuration.urls.determinePages)
            .post({ site, language }, { headers: { "Content-Type": "application/json" } })
            .then(async (response) => {
            const result = await response.resolve();
            if (result.error) {
                this.showError(progressInfo, result.error);
            }
            else {
                this.showPageAmount(progressInfo, progressSuccess, result.amount);
                this.runIndex(site, language);
            }
        });
    }
    runIndex(site, language) {
        const progressDiv = this.getProgressContainer(site, language);
        const progressBar = progressDiv?.querySelector(".js-crawler-progress-bar");
        if (!progressBar)
            return;
        const offsetKey = `${site}-${language}`;
        const offset = this.progressCache[offsetKey] ?? 0;
        this.postRequest(this.configuration.urls.indexPages, {
            site,
            language,
            offset,
        }).then(async (response) => {
            const result = await response.resolve();
            if (result.status) {
                this.showSuccess(progressBar, result.total);
                this.toggleButtons(false);
            }
            else {
                this.progressCache[offsetKey] = parseInt(result.nextOffset, 10);
                await this.process(language, result, progressBar);
                this.runIndex(site, language);
            }
        });
    }
    async process(language, result, progressBar) {
        let current = result.current;
        let total = result.total;
        let percentage = Math.round((current / total) * 100);
        this.updateProgressBar(progressBar, current, total, percentage);
        for (let page in result.pages) {
            if (result.pages.hasOwnProperty(page)) {
                let pageId = result.pages[page];
                const response = await FrontendRequest.request({
                    pageId: pageId,
                    languageId: language,
                });
                const frontendResult = await response.resolve();
                await this.processRelevantWords(pageId, language, frontendResult);
                current++;
                percentage = Math.round((current / total) * 100);
                this.updateProgressBar(progressBar, current, total, percentage);
            }
        }
    }
    async processRelevantWords(pageId, language, result) {
        worker.set(false, result.locale);
        const content = analysis.createContent({
            url: result.url,
            title: result.title,
            body: result.body,
            locale: result.locale,
        });
        const response = await analysis.runResearch("getProminentWordsForInternalLinking", content);
        saveProminentWords(this.configuration.urls.prominentWords, "pages", pageId, language, response.result);
    }
    updateProgressBar(progressBar, current, total, percentage) {
        progressBar.innerText = current + "/" + total;
        progressBar.style.width = percentage + "%";
    }
    showError(container, message) {
        container.innerHTML = message;
        container.classList.remove("alert-info");
        container.classList.add("alert-danger");
    }
    showPageAmount(progressInfo, successContainer, amount) {
        progressInfo.classList.add("hide");
        successContainer.classList.remove("hide");
        successContainer.innerHTML = successContainer.innerHTML.replace("%d", String(amount));
    }
    showSuccess(progressBar, total) {
        progressBar.classList.remove("progress-bar-info");
        progressBar.classList.add("progress-bar-success");
        progressBar.style.width = "100%";
        progressBar.innerHTML = `${total} pages have been indexed.`;
    }
    toggleButtons(disabled) {
        let buttons = document.querySelectorAll(".js-crawler-button");
        buttons.forEach((button) => {
            button.disabled = disabled;
        });
    }
    removeSavedProgress(site, language) {
        document.querySelector(`#saved-progress-${site}-${language}`)?.remove();
    }
    getProgressContainer(site, language) {
        return document.querySelector(`#progress-${site}-${language}`);
    }
    postRequest(url, data) {
        return new AjaxRequest(url).post(data, {
            headers: { "Content-Type": "application/json" },
        });
    }
}
export default new Crawler();
