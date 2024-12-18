<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Service\PageLayoutHeader;

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use YoastSeoForTypo3\YoastSeo\Traits\BackendUserTrait;
use YoastSeoForTypo3\YoastSeo\Utility\YoastUtility;

class VisibilityChecker
{
    use BackendUserTrait;

    /**
     * @param array<string, string> $pageRecord
     */
    public function shouldShowPreview(int $pageId, array $pageRecord): bool
    {
        if (!$this->isSnippetPreviewEnabled($pageId, $pageRecord)) {
            return false;
        }

        $allowedDoktypes = YoastUtility::getAllowedDoktypes();
        return isset($pageRecord['doktype']) && in_array((int)$pageRecord['doktype'], $allowedDoktypes, true);
    }

    /**
     * @param array<string, string> $pageRecord
     */
    protected function isSnippetPreviewEnabled(int $pageId, array $pageRecord): bool
    {
        $backendUser = $this->getBackendUser();
        if (!$backendUser->check('non_exclude_fields', 'pages:tx_yoastseo_snippetpreview')) {
            return false;
        }

        if ($this->hideFromUserSettings($backendUser)) {
            return false;
        }

        $pageTsConfig = $this->getPageTsConfig($pageId);
        if ((int)($pageTsConfig['mod.']['web_SeoPlugin.']['disableSnippetPreview'] ?? 0) === 1) {
            return false;
        }

        return !((bool)($pageRecord['tx_yoastseo_hide_snippet_preview'] ?? false));
    }

    protected function hideFromUserSettings(BackendUserAuthentication $backendUser): bool
    {
        if (isset($backendUser->uc['hideYoastInPageModule'])) {
            return (bool)$backendUser->uc['hideYoastInPageModule'];
        }

        return false;
    }

    /**
     * @return array<string, mixed>
     */
    protected function getPageTsConfig(int $pageId): array
    {
        return BackendUtility::getPagesTSconfig($pageId);
    }
}
