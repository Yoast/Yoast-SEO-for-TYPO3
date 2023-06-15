<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Widgets\Provider;

use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Type\Bitmask\Permission;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class OrphanedContentDataProvider implements PageProviderInterface
{
    private array $excludedDoktypes;
    private int $limit;

    public function __construct(array $excludedDoktypes, int $limit)
    {
        $this->excludedDoktypes = $excludedDoktypes;
        $this->limit = $limit ?: 5;
    }

    public function getPages(): array
    {
        $qb = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('sys_refindex');

        $constraints = [
            $qb->expr()->eq('ref_table', $qb->createNamedParameter('pages')),
            $qb->expr()->notIn(
                'field',
                $qb->createNamedParameter([
                    'l10n_parent',
                    'db_mountpoints'
                ], Connection::PARAM_STR_ARRAY)
            )
        ];

        $refs = $qb->select('ref_uid')
            ->from('sys_refindex')
            ->where(...$constraints)
            ->groupBy('ref_uid')
            ->execute()
            ->fetchAllAssociative();

        $pageIds = [];
        foreach ($refs as $ref) {
            $pageIds[] = $ref['ref_uid'];
        }

        $items = [];
        $counter = 0;
        $iterator = 0;

        $constraints = [];
        if (count($pageIds)) {
            $constraints = [
                $qb->expr()->notIn('p.doktype', $this->excludedDoktypes),
                $qb->expr()->notIn('p.uid', $pageIds),
            ];
        }

        $qb = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('pages');
        while ($counter < $this->limit) {
            $row = $qb->select('p.*')
                ->from('pages', 'p')
                ->where(...$constraints)
                ->orderBy('tstamp', 'DESC')
                ->setFirstResult($iterator)
                ->setMaxResults(1)
                ->execute()
                ->fetchAssociative();

            if ($row === false) {
                // Likely fewer pages than the limit, prevent infinite loop
                break;
            }

            $iterator++;

            if (!$this->getBackendUser()->doesUserHaveAccess($row, Permission::PAGE_SHOW)) {
                continue;
            }

            $items[] = $row;
            $counter++;
        }

        return $items;
    }

    protected function getBackendUser(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }
}
