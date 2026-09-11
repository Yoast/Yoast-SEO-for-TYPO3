<?php

/**
 * This file is part of the "yoast_seo" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Utility;

use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Platform\PlatformInformation;
use TYPO3\CMS\Core\Database\Query\QueryHelper;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Type\Bitmask\Permission;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use YoastSeoForTypo3\YoastSeo\Constants\TableNames;

class PageAccessUtility
{
    protected const MAX_DEPTH = 999;

    /** @var array<int, int[]> */
    protected static array $cache = [];

    /**
     * Resolves the given page and all accessible descendants in a breadth-first
     * manner: one query per tree level instead of one query per page node.
     *
     * @return int[]
     */
    public static function getPageIds(int $pid): array
    {
        if (isset(self::$cache[$pid])) {
            return self::$cache[$pid];
        }

        $permsClause = self::getBackendUser()->getPagePermsClause(Permission::PAGE_SHOW);

        $pageIds = [$pid];
        $seen = [$pid => true];
        $parentIds = [$pid];
        $depth = 0;

        while ($parentIds !== [] && $depth < self::MAX_DEPTH) {
            $next = [];
            foreach (self::getChildPageIds($parentIds, $permsClause) as $uid) {
                if (isset($seen[$uid])) {
                    continue;
                }
                $seen[$uid] = true;
                $pageIds[] = $uid;
                $next[] = $uid;
            }
            $parentIds = $next;
            $depth++;
        }

        return self::$cache[$pid] = $pageIds;
    }

    /**
     * @param int[] $parentIds
     * @return int[]
     */
    protected static function getChildPageIds(array $parentIds, string $permsClause): array
    {
        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        $childIds = [];

        foreach (array_chunk($parentIds, self::getMaxBindParameters()) as $chunk) {
            $queryBuilder = $connectionPool->getQueryBuilderForTable(TableNames::PAGES);
            $queryBuilder->getRestrictions()
                ->removeAll()
                ->add(GeneralUtility::makeInstance(DeletedRestriction::class));
            $queryBuilder->select('uid')
                ->from(TableNames::PAGES)
                ->where(
                    $queryBuilder->expr()->in(
                        'pid',
                        $queryBuilder->createNamedParameter($chunk, Connection::PARAM_INT_ARRAY)
                    ),
                    $queryBuilder->expr()->eq(
                        'sys_language_uid',
                        $queryBuilder->createNamedParameter(0, Connection::PARAM_INT)
                    )
                )
                ->orderBy('uid');

            if ($permsClause !== '') {
                $queryBuilder->andWhere(QueryHelper::stripLogicalOperatorPrefix($permsClause));
            }

            foreach ($queryBuilder->executeQuery()->fetchFirstColumn() as $uid) {
                $childIds[] = (int)$uid;
            }
        }

        return $childIds;
    }

    /**
     * @return int<1, max>
     */
    protected static function getMaxBindParameters(): int
    {
        $connection = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable(TableNames::PAGES);
        return max(1, PlatformInformation::getMaxBindParameters($connection->getDatabasePlatform()) - 10);
    }

    protected static function getBackendUser(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }
}
