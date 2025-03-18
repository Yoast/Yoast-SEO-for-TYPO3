import "@yoast/yoast-seo-for-typo3/dist/webcomponents.js";
import DocumentService from "@typo3/core/document-service.js";
import { setAttributes } from "@yoast/yoast-seo-for-typo3/helpers/attributes.js";
import FormEngine from "@yoast/yoast-seo-for-typo3/helpers/form-engine.js";
import SocialPreview from "@yoast/yoast-seo-for-typo3/social-preview.js";
import store from "@yoast/yoast-seo-for-typo3/store.js";
class FacebookPreview extends SocialPreview {
    async initialize(configuration) {
        await DocumentService.ready();
        this.registerFieldSelectors(configuration.fieldSelectors);
        this.setSocialType("facebook");
        this.setupListeners();
        this.syncPreview();
        this.isInitialized = true;
        store.subscribe(() => {
            if (this.isInitialized)
                this.syncPreview();
        });
    }
    setupListeners() {
        const fields = ["ogTitle", "ogDescription"];
        fields.forEach((fieldName) => {
            FormEngine.getElement(fieldName)?.addEventListener("input", () => this.syncPreview());
        });
    }
    getData() {
        const ogTitle = FormEngine.getInputElementValue("ogTitle").trim();
        const ogDesc = FormEngine.getInputElementValue("ogDescription").trim();
        const seoTitle = FormEngine.getInputElementValue("title");
        const pageTitle = FormEngine.getInputElementValue("pageTitle");
        const seoDesc = FormEngine.getInputElementValue("description");
        return {
            title: ogTitle || seoTitle || pageTitle || "",
            description: ogDesc || seoDesc || "...",
        };
    }
    syncPreview() {
        const previewElement = this.getElement();
        if (!previewElement)
            return;
        const values = this.getData();
        setAttributes(previewElement, {
            title: values.title,
            description: values.description,
        });
    }
}
export default new FacebookPreview();
