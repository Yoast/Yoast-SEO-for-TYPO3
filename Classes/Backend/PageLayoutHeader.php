<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Backend;

use TYPO3\CMS\Backend\Controller\PageLayoutController;
use TYPO3\CMS\Backend\Template\ModuleTemplate;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;
use YoastSeoForTypo3\YoastSeo\Service\SnippetPreviewService;
use YoastSeoForTypo3\YoastSeo\Service\UrlService;
use YoastSeoForTypo3\YoastSeo\Utility\YoastUtility;

class PageLayoutHeader
{
    public function __construct(
        protected UrlService $urlService,
        protected SnippetPreviewService $snippetPreviewService
    ) {
    }

    /**
     * @param array<string, string>|null $params
     */
    public function render(array $params = null, PageLayoutController|ModuleTemplate|null $parentObj = null): string
    {
        $languageId = $this->getLanguageId();
        $pageId = (int)$_GET['id'];
        $currentPage = $this->getCurrentPage($pageId, $languageId, $parentObj);

        if (!is_array($currentPage) || !$this->shouldShowPreview($pageId, $currentPage)) {
            return '';
        }

        $this->snippetPreviewService->buildSnippetPreview(
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
            'targetElementId' => uniqid('_YoastSEO_panel_')
        ]);
        return $templateView->render();
    }

    /**
     * @return array<string, string>|null
     */
    protected function getCurrentPage(int $pageId, int $languageId, PageLayoutController|ModuleTemplate|null $parentObj): ?array
    {
        if ((!$parentObj instanceof PageLayoutController && !$parentObj instanceof ModuleTemplate) || $pageId <= 0) {
            return null;
        }

        if ($languageId === 0) {
            return BackendUtility::getRecord(
                'pages',
                $pageId
            );
        }

        if ($languageId > 0) {
            $overlayRecords = BackendUtility::getRecordLocalization(
                'pages',
                $pageId,
                $languageId
            );

            if (is_array($overlayRecords) && array_key_exists(0, $overlayRecords) && is_array($overlayRecords[0])) {
                return $overlayRecords[0];
            }
        }

        return null;
    }

    /**
     * @param array<string, string> $pageRecord
     */
    protected function shouldShowPreview(int $pageId, array $pageRecord): bool
    {
        if (!YoastUtility::snippetPreviewEnabled($pageId, $pageRecord)) {
            return false;
        }

        $allowedDoktypes = YoastUtility::getAllowedDoktypes();
        return isset($pageRecord['doktype']) && in_array((int)$pageRecord['doktype'], $allowedDoktypes, true);
    }

    protected function getLanguageId(): int
    {
        $moduleData = (array)BackendUtility::getModuleData(['language'], [], 'web_layout');
        return (int)$moduleData['language'];
    }
}
