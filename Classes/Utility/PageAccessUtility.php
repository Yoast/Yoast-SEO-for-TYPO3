<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Utility;

use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Database\QueryGenerator;
use TYPO3\CMS\Core\Type\Bitmask\Permission;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class PageAccessUtility
{
    protected static array $cache = [];

    public static function getPageIds(int $pid): array
    {
        if (self::$cache !== []) {
            return self::$cache;
        }

        $backendUser = self::getBackendUser();
        $perms_clause = $backendUser->getPagePermsClause(Permission::PAGE_SHOW);

        return self::$cache = GeneralUtility::intExplode(
            ',',
            GeneralUtility::makeInstance(QueryGenerator::class)->getTreeList($pid, 999, 0, $perms_clause)
        );
    }

    protected static function getBackendUser(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }
}
