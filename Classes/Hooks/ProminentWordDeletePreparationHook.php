<?php

/**
 * This file is part of the "yoast_seo" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Hooks;

use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use YoastSeoForTypo3\YoastSeo\Constants\TableNames;

#[Autoconfigure(public: true)]
class ProminentWordDeletePreparationHook
{
    public function __construct(
        protected readonly ConnectionPool $connectionPool,
    ) {}

    /**
     * DataHandler refuses to delete a page for editors without modify access to
     * the prominent word table when rows of it exist in the page tree. The rows
     * are regenerated analysis results, so they are removed before that check.
     */
    public function processCmdmap_preProcess(
        string $command,
        string $table,
        string|int $id,
        mixed $value,
        DataHandler $dataHandler,
    ): void {
        if ($command !== 'delete' || $table !== TableNames::PAGES) {
            return;
        }

        $queryBuilder = $this->connectionPool->getQueryBuilderForTable(TableNames::PROMINENT_WORD);
        $queryBuilder->delete(TableNames::PROMINENT_WORD)
            ->where(
                $queryBuilder->expr()->in(
                    'pid',
                    $queryBuilder->createNamedParameter($this->getPageIdsInTree((int)$id), Connection::PARAM_INT_ARRAY)
                )
            )
            ->executeStatement();
    }

    /**
     * @return list<int>
     */
    protected function getPageIdsInTree(int $pageId): array
    {
        $pageIds = [$pageId];
        $parentIds = [$pageId];
        while ($parentIds !== []) {
            $queryBuilder = $this->connectionPool->getQueryBuilderForTable(TableNames::PAGES);
            $queryBuilder->getRestrictions()->removeAll();
            $parentIds = array_map('intval', $queryBuilder->select('uid')
                ->from(TableNames::PAGES)
                ->where(
                    $queryBuilder->expr()->in(
                        'pid',
                        $queryBuilder->createNamedParameter($parentIds, Connection::PARAM_INT_ARRAY)
                    )
                )
                ->executeQuery()
                ->fetchFirstColumn());
            $pageIds = [...$pageIds, ...$parentIds];
        }
        return $pageIds;
    }
}
