<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Service;

use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use YoastSeoForTypo3\YoastSeo\Utility\JavascriptUtility;
use YoastSeoForTypo3\YoastSeo\Utility\JsonConfigUtility;
use YoastSeoForTypo3\YoastSeo\Utility\PathUtility;

class SnippetPreviewService
{
    public function __construct(
        protected UrlService $urlService,
        protected PageRenderer $pageRenderer,
        protected LocaleService $localeService
    ) {
    }

    /**
     * @param array<string, mixed> $currentData
     * @param array<string, mixed> $additionalConfiguration
     */
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
            'translations' => $this->localeService->getTranslations(),
        ];
        $jsonConfigUtility = GeneralUtility::makeInstance(JsonConfigUtility::class);
        $jsonConfigUtility->addConfig(array_merge($config, $additionalConfiguration));

        JavascriptUtility::loadJavascript($this->pageRenderer);

        if (GeneralUtility::makeInstance(Typo3Version::class)->getMajorVersion() >= 13) {
            $this->pageRenderer->loadJavaScriptModule(
                '@yoast/yoast-seo-for-typo3/yoastModalEs6.js',
            );
        } else {
            $this->pageRenderer->loadRequireJsModule(
                'TYPO3/CMS/YoastSeo/yoastModal',
            );
        }
        $this->pageRenderer->addCssFile('EXT:yoast_seo/Resources/Public/CSS/yoast.min.css');
    }
}
