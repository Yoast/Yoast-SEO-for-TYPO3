<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Service\Crawler;

use TYPO3\CMS\Backend\Routing\UriBuilder;
use YoastSeoForTypo3\YoastSeo\Service\Javascript\JavascriptService;
use YoastSeoForTypo3\YoastSeo\Service\Javascript\JsonConfigService;
use YoastSeoForTypo3\YoastSeo\Service\LocaleService;
use YoastSeoForTypo3\YoastSeo\Utility\PathUtility;

class CrawlerJavascriptConfigService
{
    public function __construct(
        protected JavascriptService $javascriptService,
        protected JsonConfigService $jsonConfigService,
        protected UriBuilder $uriBuilder,
        protected LocaleService $localeService
    ) {}

    public function addJavascriptConfig(): void
    {
        $this->javascriptService->loadPluginJavascript();
        $this->jsonConfigService->addConfig([
            'urls' => [
                'workerUrl' => PathUtility::getPublicPathToResources() . '/JavaScript/dist/worker.js',
                'preview' => (string)$this->uriBuilder->buildUriFromRoute('ajax_yoast_preview'),
                'determinePages' => (string)$this->uriBuilder->buildUriFromRoute('ajax_yoast_crawler_determine_pages'),
                'indexPages' => (string)$this->uriBuilder->buildUriFromRoute('ajax_yoast_crawler_index_pages'),
                'prominentWords' => (string)$this->uriBuilder->buildUriFromRoute('ajax_yoast_prominent_words'),
            ],
            'translations' => [$this->localeService->getTranslations()],
            'supportedLanguages' => $this->localeService->getSupportedLanguages(),
        ]);
    }
}
