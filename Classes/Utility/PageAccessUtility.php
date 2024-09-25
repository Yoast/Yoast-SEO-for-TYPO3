<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Utility;

use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryHelper;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Type\Bitmask\Permission;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class PageAccessUtility
{
    /** @var int[] */
    protected static array $cache = [];

    /**
     * @return int[]
     */
    public static function getPageIds(int $pid): array
    {
        if (self::$cache !== []) {
            return self::$cache;
        }

        $backendUser = self::getBackendUser();
        $perms_clause = $backendUser->getPagePermsClause(Permission::PAGE_SHOW);

        return self::$cache = GeneralUtility::intExplode(
            ',',
            self::getTreeList($pid, 999, 0, $perms_clause)
        );
    }

    /**
     * Copied from EXT:core, removed in v12
     */
    protected static function getTreeList(int $id, int $depth, int $begin = 0, string $permClause = ''): string
    {
        if ($id < 0) {
            $id = abs($id);
        }
        $theList = $begin === 0 ? $id : '';
        if (!($id && $depth > 0)) {
            return (string)$theList;
        }

        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('pages');
        $queryBuilder->getRestrictions()->removeAll()->add(GeneralUtility::makeInstance(DeletedRestriction::class));
        $queryBuilder->select('uid')
            ->from('pages')
            ->where(
                $queryBuilder->expr()->eq('pid', $queryBuilder->createNamedParameter($id, Connection::PARAM_INT)),
                $queryBuilder->expr()->eq('sys_language_uid', 0)
            )
            ->orderBy('uid');
        if ($permClause !== '') {
            $queryBuilder->andWhere(QueryHelper::stripLogicalOperatorPrefix($permClause));
        }
        foreach ($queryBuilder->executeQuery()->fetchAllAssociative() as $row) {
            if ($begin <= 0) {
                $theList .= ',' . $row['uid'];
            }
            if ($depth > 1) {
                $theSubList = self::getTreeList($row['uid'], $depth - 1, $begin - 1, $permClause);
                if (!empty($theList) && !empty($theSubList) && ($theSubList[0] !== ',')) {
                    $theList .= ',';
                }
                $theList .= $theSubList;
            }
        }
        return (string)$theList;
    }

    protected static function getBackendUser(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }
}
