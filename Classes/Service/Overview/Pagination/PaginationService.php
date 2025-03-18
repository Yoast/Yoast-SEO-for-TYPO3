<?php

/**
 * This file is part of the "yoast_seo" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

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
