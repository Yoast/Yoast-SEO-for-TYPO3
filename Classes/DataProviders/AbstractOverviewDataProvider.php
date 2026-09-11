<?php

/**
 * This file is part of the "yoast_seo" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\DataProviders;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Platform\PlatformInformation;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use YoastSeoForTypo3\YoastSeo\Constants\TableNames;
use YoastSeoForTypo3\YoastSeo\Service\Overview\Dto\DataProviderRequest;
use YoastSeoForTypo3\YoastSeo\Utility\PageAccessUtility;

abstract class AbstractOverviewDataProvider implements OverviewDataProviderInterface
{
    protected const PAGES_FIELDS = [
        'uid',
        'doktype',
        'hidden',
        'starttime',
        'endtime',
        'fe_group',
        't3ver_state',
        'nav_hide',
        'is_siteroot',
        'module',
        'content_from_pid',
        'extendToSubpages',
        'sys_language_uid',
        'title',
        'seo_title',
        'tx_yoastseo_score_readability',
        'tx_yoastseo_score_seo',
    ];

    protected DataProviderRequest $dataProviderRequest;

    protected ?int $numberOfItems = null;

    public function initialize(DataProviderRequest $dataProviderRequest): void
    {
        $this->dataProviderRequest = $dataProviderRequest;
        $this->numberOfItems = null;
    }

    /**
     * Builds the fully constrained query for a chunk of page ids, selecting an
     * explicit column list. The query is not executed; the base class applies
     * counting and pagination on top of it.
     *
     * @param int[] $pageIds
     */
    abstract protected function createQueryBuilder(array $pageIds): QueryBuilder;

    /**
     * @return array<int, array<string, mixed>>
     */
    public function process(int $offset = 0, int $limit = 0): array
    {
        $chunks = $this->getPageIdChunks();

        // Fast path: a single chunk (the common case on MySQL/MariaDB/PostgreSQL)
        // lets the database do the windowing in one query.
        if (count($chunks) <= 1) {
            $queryBuilder = $this->createQueryBuilder($chunks[0] ?? [])->orderBy('uid');
            if ($limit > 0) {
                $queryBuilder->setFirstResult(max(0, $offset))->setMaxResults($limit);
            }
            return $queryBuilder->executeQuery()->fetchAllAssociative();
        }

        // Multi-chunk path: window across chunks (very large trees on SQLite).
        $items = [];
        $toSkip = max(0, $offset);
        $remaining = $limit;
        foreach ($chunks as $chunk) {
            if ($limit > 0 && $remaining <= 0) {
                break;
            }
            $queryBuilder = $this->createQueryBuilder($chunk)->orderBy('uid');
            if ($limit > 0) {
                $chunkCount = $this->countChunk($chunk);
                if ($toSkip >= $chunkCount) {
                    $toSkip -= $chunkCount;
                    continue;
                }
                $queryBuilder->setFirstResult($toSkip)->setMaxResults($remaining);
                $toSkip = 0;
            }
            foreach ($queryBuilder->executeQuery()->fetchAllAssociative() as $row) {
                $items[] = $row;
                $remaining--;
            }
        }
        return $items;
    }

    public function getNumberOfItems(): int
    {
        if ($this->numberOfItems === null) {
            $count = 0;
            foreach ($this->getPageIdChunks() as $chunk) {
                $count += $this->countChunk($chunk);
            }
            $this->numberOfItems = $count;
        }
        return $this->numberOfItems;
    }

    /**
     * @param int[] $pageIds
     */
    protected function countChunk(array $pageIds): int
    {
        return (int)$this->createQueryBuilder($pageIds)
            ->count('uid')
            ->executeQuery()
            ->fetchOne();
    }

    /**
     * The field a page id restriction is applied to: the translation parent in a
     * translated language, the page uid itself in the default language.
     */
    protected function getRestrictionField(): string
    {
        if ($this->dataProviderRequest->getLanguage() > 0) {
            return (string)$GLOBALS['TCA'][TableNames::PAGES]['ctrl']['transOrigPointerField'];
        }
        return 'uid';
    }

    /**
     * @return array<int, int[]>
     */
    protected function getPageIdChunks(): array
    {
        return array_chunk($this->getPageIds(), $this->getMaxBindParameters() - 10);
    }

    /**
     * @return int[]
     */
    protected function getPageIds(): array
    {
        return PageAccessUtility::getPageIds($this->dataProviderRequest->getId());
    }

    /**
     * @return int<999, max>
     */
    protected function getMaxBindParameters(): int
    {
        $connection = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable(TableNames::PAGES);
        return max(999, PlatformInformation::getMaxBindParameters($connection->getDatabasePlatform()));
    }
}
