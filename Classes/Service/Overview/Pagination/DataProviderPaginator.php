<?php

/**
 * This file is part of the "yoast_seo" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Service\Overview\Pagination;

use TYPO3\CMS\Core\Pagination\AbstractPaginator;
use YoastSeoForTypo3\YoastSeo\DataProviders\OverviewDataProviderInterface;

/**
 * Paginates an overview data provider at the database level: only the records of
 * the current page are fetched, the total is obtained via a COUNT query.
 */
final class DataProviderPaginator extends AbstractPaginator
{
    /** @var array<int, array<string, mixed>> */
    private array $paginatedItems = [];

    public function __construct(
        private readonly OverviewDataProviderInterface $dataProvider,
        int $currentPageNumber = 1,
        int $itemsPerPage = 10,
    ) {
        $this->setCurrentPageNumber($currentPageNumber);
        $this->setItemsPerPage($itemsPerPage);
        $this->updateInternalState();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getPaginatedItems(): array
    {
        return $this->paginatedItems;
    }

    protected function updatePaginatedItems(int $itemsPerPage, int $offset): void
    {
        $this->paginatedItems = $this->dataProvider->process($offset, $itemsPerPage);
    }

    protected function getTotalAmountOfItems(): int
    {
        return $this->dataProvider->getNumberOfItems();
    }

    protected function getAmountOfItemsOnCurrentPage(): int
    {
        return count($this->paginatedItems);
    }
}
