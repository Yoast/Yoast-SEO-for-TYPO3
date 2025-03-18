<?php

/**
 * This file is part of the "yoast_seo" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Service\Crawler;

use TYPO3\CMS\Backend\Routing\UriBuilder;
use YoastSeoForTypo3\YoastSeo\Service\Javascript\JsonTranslationsService;
use YoastSeoForTypo3\YoastSeo\Service\LocaleService;
use YoastSeoForTypo3\YoastSeo\Utility\PathUtility;

class CrawlerJavascriptConfigService
{
    public function __construct(
        protected JsonTranslationsService $jsonConfigService,
        protected UriBuilder $uriBuilder,
        protected LocaleService $localeService
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function getJavascriptConfig(): array
    {
        return [
            'urls' => [
                'workerUrl' => PathUtility::getPublicPathToResources() . '/JavaScript/dist/worker.js',
                'determinePages' => (string)$this->uriBuilder->buildUriFromRoute('ajax_yoast_crawler_determine_pages'),
                'indexPages' => (string)$this->uriBuilder->buildUriFromRoute('ajax_yoast_crawler_index_pages'),
                'prominentWords' => (string)$this->uriBuilder->buildUriFromRoute('ajax_yoast_prominent_words'),
            ],
            'translations' => [$this->localeService->getTranslations()],
            'supportedLanguages' => $this->localeService->getSupportedLanguages(),
        ];
    }
}
