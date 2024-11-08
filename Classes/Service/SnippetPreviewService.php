<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Service;

use TYPO3\CMS\Core\Page\PageRenderer;
use YoastSeoForTypo3\YoastSeo\Service\Javascript\JavascriptService;
use YoastSeoForTypo3\YoastSeo\Service\Javascript\JsonConfigService;
use YoastSeoForTypo3\YoastSeo\Utility\PathUtility;

class SnippetPreviewService
{
    public function __construct(
        protected UrlService $urlService,
        protected PageRenderer $pageRenderer,
        protected LocaleService $localeService,
        protected JavascriptService $javascriptService,
        protected JsonConfigService $jsonConfigService
    ) {}

    /**
     * @param array<string, mixed> $currentData
     * @param array<string, mixed> $additionalConfiguration
     */
    public function buildSnippetPreview(
        string $previewUrl,
        array $currentData,
        array $additionalConfiguration
    ): void {
        $config = [
            'urls' => [
                'workerUrl' => PathUtility::getPublicPathToResources() . '/JavaScript/dist/worker.js',
                'previewUrl' => $previewUrl,
                'saveScores' => $this->urlService->getSaveScoresUrl(),
                'prominentWords' => $this->urlService->getProminentWordsUrl(),
            ],
            'isCornerstoneContent' => (bool)($currentData['tx_yoastseo_cornerstone'] ?? false),
            'focusKeyphrase' => [
                'keyword' => (string)($currentData['tx_yoastseo_focuskeyword'] ?? ''),
                'synonyms' => (string)($currentData['tx_yoastseo_focuskeyword_synonyms'] ?? ''),
            ],
            'translations' => [$this->localeService->getTranslations()],
        ];

        $this->javascriptService->loadPluginJavascript();
        $this->javascriptService->loadModalJavascript();

        $this->jsonConfigService->addConfig(array_merge($config, $additionalConfiguration));

        $this->pageRenderer->addCssFile('EXT:yoast_seo/Resources/Public/CSS/yoast.min.css');
    }
}
