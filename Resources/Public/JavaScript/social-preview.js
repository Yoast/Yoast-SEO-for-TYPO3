import YoastConfiguration from "@yoast/yoast-seo-for-typo3/yoast-configuration.js";
const SOCIAL_CONFIG = {
    facebook: {
        tagName: "yoast-facebook-preview",
        fieldKey: "og_image",
        containerKey: "ogImageContainer",
    },
    twitter: {
        tagName: "yoast-twitter-preview",
        fieldKey: "twitter_image",
        containerKey: "twitterImageContainer",
    },
};
export default class SocialPreview {
    constructor() {
        this.previewElement = null;
        this.observer = null;
        this.debounceTimer = null;
        this.socialType = "facebook";
        this.isInitialized = false;
    }
    setSocialType(type) {
        this.socialType = type;
        this.initializeElement();
    }
    getElement() {
        if (!this.previewElement) {
            this.initializeElement();
        }
        return this.previewElement;
    }
    registerFieldSelectors(selectors) {
        Object.entries(selectors).forEach(([field, selector]) => {
            if (selector) {
                YoastConfiguration.setFieldSelector(field, selector);
            }
        });
    }
    initializeElement() {
        const config = SOCIAL_CONFIG[this.socialType];
        this.previewElement = document.querySelector(config.tagName);
        if (!this.previewElement) {
            return;
        }
        this.setupImageClickAction();
        this.setupFileObserver();
    }
    setupImageClickAction() {
        if (!this.previewElement)
            return;
        this.previewElement.onImageClick = () => this.triggerFileBrowser();
    }
    setupFileObserver() {
        const containerId = this.getFileContainerSelector();
        const container = containerId ? document.getElementById(containerId) : null;
        if (!container)
            return;
        this.disconnectObserver();
        this.observer = new MutationObserver(() => this.debouncedSyncImage());
        this.observer.observe(container, { childList: true, subtree: true });
    }
    triggerFileBrowser() {
        const { fieldKey, containerKey } = SOCIAL_CONFIG[this.socialType];
        let button = document.querySelector(`[data-local-field="${fieldKey}"] button`);
        if (button === null) {
            const containerFieldSelector = YoastConfiguration.getFieldSelector(containerKey);
            button = document.querySelector(`#${containerFieldSelector} button`);
        }
        button?.click();
    }
    debouncedSyncImage() {
        if (this.debounceTimer)
            window.clearTimeout(this.debounceTimer);
        this.debounceTimer = window.setTimeout(() => {
            this.syncImageToPreview();
            this.debounceTimer = null;
        }, 300);
    }
    syncImageToPreview() {
        const containerId = this.getFileContainerSelector();
        if (!containerId || !this.previewElement)
            return;
        const img = document.querySelector(`#${containerId} .t3js-image-manipulation-preview img`);
        // Fallback to " " to clear the image in the web component
        const src = img?.getAttribute("src") || " ";
        this.previewElement.setAttribute("image-url", src);
    }
    getFileContainerSelector() {
        const { containerKey } = SOCIAL_CONFIG[this.socialType];
        return YoastConfiguration.getFieldSelector(containerKey);
    }
    destroy() {
        this.disconnectObserver();
        if (this.debounceTimer)
            window.clearTimeout(this.debounceTimer);
    }
    disconnectObserver() {
        this.observer?.disconnect();
        this.observer = null;
    }
}
