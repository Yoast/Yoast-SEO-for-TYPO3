<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\DataProviders;

use Doctrine\DBAL\Result;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use YoastSeoForTypo3\YoastSeo\Utility\YoastUtility;

class OrphanedContentDataProvider extends AbstractOverviewDataProvider
{
    protected array $referencedPages = [];

    public function getResults(array $pageIds = []): ?Result
    {
        if ($this->referencedPages === []) {
            $this->setReferencedPages();
        }

        $qb = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('pages');

        $constraints = [
            $qb->expr()->in('doktype', YoastUtility::getAllowedDoktypes()),
            $qb->expr()->eq('sys_language_uid', (int)$this->callerParams['language'])
        ];
        if (count($this->referencedPages) > 0) {
            $constraints[] = $qb->expr()->notIn('uid', $this->referencedPages);
        }
        if (count($pageIds) > 0) {
            $constraints[] = $qb->expr()->in(
                (int)$this->callerParams['language'] > 0 ? $GLOBALS['TCA']['pages']['ctrl']['transOrigPointerField'] : 'uid',
                $pageIds
            );
        }

        return $qb->select('*')
            ->from('pages')
            ->where(...$constraints)
            ->executeQuery();
    }

    protected function setReferencedPages(): void
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

        $references = $qb->select('ref_uid')
            ->from('sys_refindex')
            ->where(...$constraints)
            ->groupBy('ref_uid')
            ->executeQuery()
            ->fetchAllAssociative();

        foreach ($references as $ref) {
            $this->referencedPages[] = $ref['ref_uid'];
        }
    }
}
