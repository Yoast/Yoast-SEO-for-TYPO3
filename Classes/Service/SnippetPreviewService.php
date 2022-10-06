<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Service;

use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use YoastSeoForTypo3\YoastSeo\Utility\JsonConfigUtility;
use YoastSeoForTypo3\YoastSeo\Utility\PathUtility;
use YoastSeoForTypo3\YoastSeo\Utility\YoastUtility;

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
    ) {
        $publicResourcesPath = PathUtility::getPublicPathToResources();

        $config = [
            'urls' => [
                'workerUrl' => $publicResourcesPath . '/JavaScript/dist/worker.js',
                'previewUrl' => $previewUrl,
                'saveScores' => $this->urlService->getSaveScoresUrl(),
                'prominentWords' => $this->urlService->getProminentWordsUrl(),
            ],
            'useKeywordDistribution' => YoastUtility::isPremiumInstalled(),
            'useRelevantWords' => YoastUtility::isPremiumInstalled(),
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

        if (YoastUtility::inProductionMode() === true) {
            $this->pageRenderer->loadRequireJsModule('TYPO3/CMS/YoastSeo/dist/plugin');
        } else {
            $this->pageRenderer->addHeaderData(
                '<script type="text/javascript" src="https://localhost:3333/typo3conf/ext/yoast_seo/Resources/Public/JavaScript/dist/plugin.js" async></script>'
            );
        }

        $this->pageRenderer->loadRequireJsModule('TYPO3/CMS/YoastSeo/yoastModal');
        $this->pageRenderer->addCssFile('EXT:yoast_seo/Resources/Public/CSS/yoast.min.css');
    }
}
