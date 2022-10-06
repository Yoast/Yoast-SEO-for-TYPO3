<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Backend;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;
use YoastSeoForTypo3\YoastSeo\Service\SnippetPreviewService;
use YoastSeoForTypo3\YoastSeo\Utility\YoastUtility;

class PageLayoutHeader extends AbstractPageLayoutHeader
{
    public function render(array $params = null, $parentObj = null): string
    {
        $languageId = $this->getLanguageId();
        $pageId = $this->getPageId();
        $currentPage = $this->getCurrentPage($pageId, $languageId, $parentObj);

        if (!is_array($currentPage) || !$this->shouldShowPreview($pageId, $currentPage)) {
            return '';
        }

        $this->pageHeaderService->setSnippetPreviewEnabled(true)
            ->setLanguageId($languageId)
            ->setPageId($pageId);

        $snippetPreviewService = GeneralUtility::makeInstance(SnippetPreviewService::class);
        $snippetPreviewService->buildSnippetPreview(
            $this->urlService->getPreviewUrl($pageId, $languageId),
            $currentPage,
            [
                'data' => [
                    'table' => 'pages',
                    'uid' => $pageId,
                    'pid' => $currentPage['pid'],
                    'languageId' => $languageId
                ],
                'fieldSelectors' => [],
            ]
        );

        return $this->renderHtml();
    }

    protected function renderHtml(): string
    {
        $templateView = GeneralUtility::makeInstance(StandaloneView::class);
        $templateView->setTemplatePathAndFilename(
            GeneralUtility::getFileAbsFileName('EXT:yoast_seo/Resources/Private/Templates/PageLayout/Header.html')
        );
        $templateView->assignMultiple([
            'targetElementId' => uniqid('_YoastSEO_panel_'),
            'premiumInstalled' => YoastUtility::isPremiumInstalled(),
            'premiumLink' => YoastUtility::getYoastLink(
                'Go premium',
                'pagemodule-snippetpreview'
            )
        ]);
        return $templateView->render();
    }
}
