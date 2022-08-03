<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Utility;

use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Database\QueryGenerator;
use TYPO3\CMS\Core\Type\Bitmask\Permission;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class PageAccessUtility
{
    protected static $cache = [];

    public static function getPageIds(): ?array
    {
        $backendUser = self::getBackendUser();
        if ($backendUser->isAdmin()) {
            return null;
        }

        if (self::$cache !== []) {
            return self::$cache;
        }

        $webMounts = $backendUser->returnWebmounts();
        $perms_clause = $backendUser->getPagePermsClause(Permission::PAGE_SHOW);

        $pageIds = '';
        foreach ($webMounts as $mount) {
            $pageIds .= (!empty($pageIds) ? ',' : '') . self::getTreeList((int)$mount, $perms_clause);
        }

        return self::$cache = GeneralUtility::intExplode(',', $pageIds);
    }

    protected static function getTreeList(int $pid, string $perms_clause): string
    {
        return (string)GeneralUtility::makeInstance(QueryGenerator::class)->getTreeList($pid, 999, 0, $perms_clause);
    }

    protected static function getBackendUser(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }
}
