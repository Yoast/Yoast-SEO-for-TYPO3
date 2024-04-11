<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Service;

use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use YoastSeoForTypo3\YoastSeo\Utility\JavascriptUtility;
use YoastSeoForTypo3\YoastSeo\Utility\JsonConfigUtility;
use YoastSeoForTypo3\YoastSeo\Utility\PathUtility;

class SnippetPreviewService
{
    protected UrlService $urlService;
    protected PageRenderer $pageRenderer;
    protected LocaleService $localeService;

    public function __construct(
        UrlService $urlService,
        PageRenderer $pageRenderer,
        LocaleService $localeService
    ) {
        $this->urlService = $urlService;
        $this->pageRenderer = $pageRenderer;
        $this->localeService = $localeService;
    }

    public function buildSnippetPreview(
        string $previewUrl,
        array $currentData,
        array $additionalConfiguration
    ): void {
        $publicResourcesPath = PathUtility::getPublicPathToResources();

        $config = [
            'urls' => [
                'workerUrl' => $publicResourcesPath . '/JavaScript/dist/worker.js',
                'previewUrl' => $previewUrl,
                'saveScores' => $this->urlService->getSaveScoresUrl(),
                'prominentWords' => $this->urlService->getProminentWordsUrl(),
            ],
            'isCornerstoneContent' => (bool)($currentData['tx_yoastseo_cornerstone'] ?? false),
            'focusKeyphrase' => [
                'keyword' => (string)($currentData['tx_yoastseo_focuskeyword'] ?? ''),
                'synonyms' => (string)($currentData['tx_yoastseo_focuskeyword_synonyms'] ?? ''),
            ],
            'labels' => $this->localeService->getLabels(),
            'translations' => [$this->localeService->getTranslations()],
        ];
        $jsonConfigUtility = GeneralUtility::makeInstance(JsonConfigUtility::class);
        $jsonConfigUtility->addConfig(array_merge($config, $additionalConfiguration));

        JavascriptUtility::loadJavascript($this->pageRenderer);

        $this->pageRenderer->loadRequireJsModule('TYPO3/CMS/YoastSeo/yoastModal');
        $this->pageRenderer->addCssFile('EXT:yoast_seo/Resources/Public/CSS/yoast.min.css');
    }
}
