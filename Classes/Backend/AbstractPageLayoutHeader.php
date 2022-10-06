<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Backend;

use TYPO3\CMS\Backend\Controller\PageLayoutController;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use YoastSeoForTypo3\YoastSeo\Service\PageHeaderService;
use YoastSeoForTypo3\YoastSeo\Service\UrlService;
use YoastSeoForTypo3\YoastSeo\Utility\YoastUtility;

abstract class AbstractPageLayoutHeader
{
    protected UrlService $urlService;
    protected PageHeaderService $pageHeaderService;

    public function __construct(
        UrlService $urlService = null,
        PageHeaderService $pageHeaderService = null
    ) {
        $this->urlService = $urlService ?? GeneralUtility::makeInstance(UrlService::class);
        $this->pageHeaderService = $pageHeaderService ?? GeneralUtility::makeInstance(PageHeaderService::class);
    }

    protected function getCurrentPage(int $pageId, int $languageId, object $parentObj): ?array
    {
        $currentPage = null;

        if ($parentObj instanceof PageLayoutController && $pageId > 0) {
            if ($languageId === 0) {
                $currentPage = BackendUtility::getRecord(
                    'pages',
                    $pageId
                );
            } elseif ($languageId > 0) {
                $overlayRecords = BackendUtility::getRecordLocalization(
                    'pages',
                    $pageId,
                    $languageId
                );

                if (is_array($overlayRecords) && array_key_exists(0, $overlayRecords) && is_array($overlayRecords[0])) {
                    $currentPage = $overlayRecords[0];
                }
            }
        }
        return $currentPage;
    }

    protected function getLanguageId(): int
    {
        $moduleData = (array)BackendUtility::getModuleData(['language'], [], 'web_layout');
        return (int)$moduleData['language'];
    }

    protected function getPageId(): int
    {
        return (int)GeneralUtility::_GET('id');
    }

    protected function shouldShowPreview(int $pageId, array $pageRecord): bool
    {
        if (!YoastUtility::snippetPreviewEnabled($pageId, $pageRecord)) {
            return false;
        }

        $allowedDoktypes = YoastUtility::getAllowedDoktypes();
        return isset($pageRecord['doktype']) && in_array((int)$pageRecord['doktype'], $allowedDoktypes, true);
    }

    abstract public function render(array $params = null, $parentObj = null): string;
}
