<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Service\Overview\Pagination;

use TYPO3\CMS\Core\Pagination\ArrayPaginator;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class PaginationService
{
    /**
     * @param array<int, array<string, mixed>> $items
     */
    public function getArrayPaginator(
        array $items,
        int $currentPage,
        int $itemsPerPage,
    ): ArrayPaginator {
        return GeneralUtility::makeInstance(
            ArrayPaginator::class,
            $items,
            $currentPage,
            $itemsPerPage
        );
    }

    public function getPagination(ArrayPaginator $arrayPaginator): Pagination
    {
        return GeneralUtility::makeInstance(Pagination::class, $arrayPaginator);
    }
}
