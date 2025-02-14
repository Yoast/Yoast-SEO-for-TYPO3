<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Service\Javascript;

use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use YoastSeoForTypo3\YoastSeo\Service\YoastEnvironmentService;

class JavascriptService
{
    public function __construct(
        protected PageRenderer $pageRenderer,
        protected YoastEnvironmentService $yoastEnvironmentService
    ) {}

    public function loadPluginJavascript(): void
    {
        if ($this->yoastEnvironmentService->isDevelopmentMode()) {
            $this->pageRenderer->addHeaderData(
                '<script type="text/javascript" src="https://localhost:3333/typo3conf/ext/yoast_seo/Resources/Public/JavaScript/dist/plugin.js" async></script>'
            );
            return;
        }

        if ($this->isEs6()) {
            $this->pageRenderer->loadJavaScriptModule(
                '@yoast/yoast-seo-for-typo3/dist/plugin.js',
            );
        } else {
            $this->pageRenderer->loadRequireJsModule(
                'TYPO3/CMS/YoastSeo/dist/plugin',
            );
        }
    }

    public function loadModalJavascript(): void
    {
        if ($this->isEs6()) {
            $this->pageRenderer->loadJavaScriptModule(
                '@yoast/yoast-seo-for-typo3/yoastModalEs6.js',
            );
        } else {
            $this->pageRenderer->loadRequireJsModule(
                'TYPO3/CMS/YoastSeo/yoastModal',
            );
        }
    }

    public function isEs6(): bool
    {
        return GeneralUtility::makeInstance(Typo3Version::class)->getMajorVersion() >= 13;
    }
}
