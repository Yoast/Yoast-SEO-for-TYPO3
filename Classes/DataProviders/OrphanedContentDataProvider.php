<?php

/**
 * This file is part of the "yoast_seo" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\DataProviders;

use Doctrine\DBAL\Result;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use YoastSeoForTypo3\YoastSeo\Constants\TableNames;
use YoastSeoForTypo3\YoastSeo\Utility\YoastUtility;

class OrphanedContentDataProvider extends AbstractOverviewDataProvider
{
    public function getKey(): string
    {
        return 'orphaned';
    }

    public function getLabel(): string
    {
        return 'LLL:EXT:yoast_seo/Resources/Private/Language/BackendModuleOverview.xlf:orphanedContent';
    }

    public function getDescription(): string
    {
        return 'LLL:EXT:yoast_seo/Resources/Private/Language/BackendModuleOverview.xlf:orphanedContent.description';
    }

    public function getLink(): ?string
    {
        return 'https://yoa.st/1ja';
    }

    /** @var int[] */
    protected array $referencedPages = [];

    /**
     * @param int[] $pageIds
     */
    public function getResults(array $pageIds = []): ?Result
    {
        if ($this->referencedPages === []) {
            $this->referencedPages = $this->getReferencedPages();
        }

        $qb = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable(TableNames::PAGES);

        $constraints = [
            $qb->expr()->in('doktype', YoastUtility::getAllowedDoktypes()),
            $qb->expr()->eq('sys_language_uid', $this->dataProviderRequest->getLanguage()),
        ];
        if (count($this->referencedPages) > 0) {
            $constraints[] = $qb->expr()->notIn('uid', $this->referencedPages);
        }
        if (count($pageIds) > 0) {
            $constraints[] = $qb->expr()->in(
                $this->dataProviderRequest->getLanguage() > 0 ? $GLOBALS['TCA'][TableNames::PAGES]['ctrl']['transOrigPointerField'] : 'uid',
                $pageIds
            );
        }

        return $qb->select('*')
            ->from(TableNames::PAGES)
            ->where(...$constraints)
            ->executeQuery();
    }

    /**
     * @return int[]
     */
    protected function getReferencedPages(): array
    {
        $qb = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable(TableNames::SYS_REFINDEX);

        $constraints = [
            $qb->expr()->eq('ref_table', $qb->createNamedParameter(TableNames::PAGES)),
            $qb->expr()->notIn(
                'field',
                $qb->createNamedParameter([
                    'l10n_parent',
                    'db_mountpoints',
                ], Connection::PARAM_STR_ARRAY)
            ),
        ];

        $references = $qb->select('ref_uid')
            ->from(TableNames::SYS_REFINDEX)
            ->where(...$constraints)
            ->groupBy('ref_uid')
            ->executeQuery()
            ->fetchAllAssociative();

        return array_column($references, 'ref_uid');
    }
}
