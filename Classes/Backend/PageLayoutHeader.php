<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Backend;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use YoastSeoForTypo3\YoastSeo\Utility\JsonConfigUtility;
use YoastSeoForTypo3\YoastSeo\Utility\PathUtility;
use YoastSeoForTypo3\YoastSeo\Utility\YoastUtility;

class PageLayoutHeader extends AbstractPageLayoutHeader
{
    /**
     * @param array|null $params
     * @param null $parentObj
     * @return string
     */
    public function render(array $params = null, $parentObj = null): string
    {
        $moduleData = $this->getModuleData();
        $pageId = $this->getPageId();
        $currentPage = $this->getCurrentPage($pageId, $moduleData, $parentObj);

        if (is_array($currentPage) && $this->shouldShowPreview($pageId, $currentPage)) {
            $this->pageHeaderService->setSnippetPreviewEnabled(true);
            $this->pageHeaderService->setModuleData($moduleData);
            $this->pageHeaderService->setPageId($pageId);

            $publicResourcesPath = PathUtility::getPublicPathToResources();

            $config = [
                'urls' => [
                    'workerUrl' => $publicResourcesPath . '/JavaScript/dist/worker.js',
                    'previewUrl' => $this->urlService->getPreviewUrl($pageId, (int)$moduleData['language']),
                    'saveScores' => $this->urlService->getSaveScoresUrl(),
                    'prominentWords' => $this->urlService->getProminentWordsUrl(),
                ],
                'useKeywordDistribution' => YoastUtility::isPremiumInstalled(),
                'useRelevantWords' => YoastUtility::isPremiumInstalled(),
                'isCornerstoneContent' => (bool)$currentPage['tx_yoastseo_cornerstone'],
                'focusKeyphrase' => [
                    'keyword' => (string)$currentPage['tx_yoastseo_focuskeyword'],
                    'synonyms' => (string)$currentPage['tx_yoastseo_focuskeyword_synonyms'],
                ],
                'labels' => $this->localeService->getLabels(),
                'fieldSelectors' => [],
                'translations' => $this->localeService->getTranslations(),
                'data' => [
                    'table' => 'pages',
                    'uid' => $pageId,
                    'languageId' => (int)$moduleData['language']
                ],
            ];
            $jsonConfigUtility = GeneralUtility::makeInstance(JsonConfigUtility::class);
            $jsonConfigUtility->addConfig($config);

            if (YoastUtility::inProductionMode() === true) {
                $this->pageRenderer->loadRequireJsModule('TYPO3/CMS/YoastSeo/dist/plugin');
            } else {
                $this->pageRenderer->addHeaderData(
                    '<script type="text/javascript" src="https://localhost:3333/typo3conf/ext/yoast_seo/Resources/Public/JavaScript/dist/plugin.js" async></script>'
                );
            }

            $this->pageRenderer->loadRequireJsModule('TYPO3/CMS/YoastSeo/yoastModal');
            $this->pageRenderer->addCssFile('EXT:yoast_seo/Resources/Public/CSS/yoast.min.css');

            return $this->getReturnHtml();
        }
        return '';
    }

    /**
     * @return string
     */
    protected function getReturnHtml(): string
    {
        $premiumText = $this->getPremiumText();
        $targetElementId = uniqid('_YoastSEO_panel_');

        $returnHtml = '
                <div class="yoast-seo-score-bar">
                    <span class="yoast-seo-score-bar--analysis yoast-seo-page" data-yoast-analysis-type="readability"></span>
                    <span class="yoast-seo-score-bar--analysis yoast-seo-page" data-yoast-analysis-type="seo"></span>
                </div>
                <div class="yoast-seo-snippet-header">
                    ' . $premiumText . '
                    <div class="yoast-snippet-header-label">Yoast SEO</div>
                </div>';

        $returnHtml .= '
            <input id="focusKeyword" style="display: none" />
            <div id="' . $targetElementId . '" class="t3-grid-cell yoast-seo yoast-seo-snippet-preview-styling" style="padding: 10px;" data-yoast-snippetpreview>
                <!-- ' . $targetElementId . ' -->
                <div class="spinner">
                  <div class="bounce bounce1"></div>
                  <div class="bounce bounce2"></div>
                  <div class="bounce bounce3"></div>
                </div>
            </div>
            <div data-yoast-analysis="readability" id="YoastPageHeaderAnalysisReadability" data-yoast-subtype="" class="hidden yoast-analysis"></div>
            <div data-yoast-analysis="seo" id="YoastPageHeaderAnalysisSeo" data-yoast-subtype="" class="hidden yoast-analysis"></div>
            ';

        return $returnHtml;
    }

    /**
     * Get premium text
     *
     * @return string
     */
    protected function getPremiumText(): string
    {
        if (!YoastUtility::isPremiumInstalled()) {
            return '
                <div class="yoast-seo-snippet-header-premium">
                    <a target="_blank" rel="noopener noreferrer" href="' . YoastUtility::getYoastLink(
                    'Go premium',
                    'pagemodule-snippetpreview'
                ) . '">
                        <i class="fa fa-star"></i>' . $GLOBALS['LANG']->sL(
                    'LLL:EXT:yoast_seo/Resources/Private/Language/BackendModule.xlf:goPremium'
                ) . '
                    </a>
                </div>';
        }
        return '';
    }
}
