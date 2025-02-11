<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Service\PageLayoutHeader;

use TYPO3\CMS\Backend\Controller\PageLayoutController;
use TYPO3\CMS\Backend\Template\ModuleTemplate;
use TYPO3\CMS\Backend\Utility\BackendUtility;

class PageDataService
{
    /**
     * @return array<string, string>|null
     */
    public function getCurrentPage(int $pageId, int $languageId, PageLayoutController|ModuleTemplate|null $parentObj): ?array
    {
        if ((!$parentObj instanceof PageLayoutController && !$parentObj instanceof ModuleTemplate) || $pageId <= 0) {
            return null;
        }

        if ($languageId === 0) {
            return $this->getPageRecord($pageId);
        }

        if ($languageId > 0) {
            $overlayRecords = $this->getOverlayRecords($pageId, $languageId);

            if (is_array($overlayRecords[0] ?? false)) {
                return $overlayRecords[0];
            }
        }

        return null;
    }

    /**
     * @return array<string, string>
     */
    protected function getPageRecord(int $pageId): array
    {
        return BackendUtility::getRecord(
            'pages',
            $pageId
        ) ?? [];
    }

    /**
     * @return array<string, string>|bool
     */
    protected function getOverlayRecords(int $pageId, int $languageId): array|bool
    {
        return BackendUtility::getRecordLocalization(
            'pages',
            $pageId,
            $languageId
        );
    }
}
