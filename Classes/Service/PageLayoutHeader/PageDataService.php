<?php

/**
 * This file is part of the "yoast_seo" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Service\PageLayoutHeader;

use TYPO3\CMS\Backend\Controller\PageLayoutController;
use TYPO3\CMS\Backend\Template\ModuleTemplate;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use YoastSeoForTypo3\YoastSeo\Constants\TableNames;

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

        if ($languageId < 0) {
            return null;
        }

        $overlayRecords = $this->getOverlayRecords($pageId, $languageId);

        if (is_array($overlayRecords[0] ?? false)) {
            return $overlayRecords[0];
        }

        return null;
    }

    /**
     * @return array<string, string>
     */
    protected function getPageRecord(int $pageId): array
    {
        return BackendUtility::getRecord(
            TableNames::PAGES,
            $pageId
        ) ?? [];
    }

    /**
     * @return array<int, array<string, mixed>>|null
     */
    protected function getOverlayRecords(int $pageId, int $languageId): ?array
    {
        $recordLocalization = BackendUtility::getRecordLocalization(
            TableNames::PAGES,
            $pageId,
            $languageId
        );
        if (is_array($recordLocalization)) {
            return $recordLocalization;
        }
        return null;
    }
}
