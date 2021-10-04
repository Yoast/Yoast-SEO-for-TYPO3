<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Pagination;

/**
 * Class SimplePagination
 *
 * @TODO This is a simplified port of the SimplePagination class from TYPO3 CMS10 and up, this is only needed for CMS9 and can be removed when CMS9 support is dropped
 */
class SimplePagination
{
    /**
     * @var \YoastSeoForTypo3\YoastSeo\Pagination\ArrayPaginator
     */
    protected $paginator;

    public function __construct(ArrayPaginator $paginator)
    {
        $this->paginator = $paginator;
    }

    public function getPreviousPageNumber(): ?int
    {
        $previousPage = $this->paginator->getCurrentPageNumber() - 1;

        if ($previousPage > $this->paginator->getNumberOfPages()) {
            return null;
        }

        return $previousPage >= $this->getFirstPageNumber()
            ? $previousPage
            : null
            ;
    }

    public function getNextPageNumber(): ?int
    {
        $nextPage = $this->paginator->getCurrentPageNumber() + 1;

        return $nextPage <= $this->paginator->getNumberOfPages()
            ? $nextPage
            : null
            ;
    }

    public function getFirstPageNumber(): int
    {
        return 1;
    }

    public function getLastPageNumber(): int
    {
        return $this->paginator->getNumberOfPages();
    }

    /**
     * @return int[]
     */
    public function getAllPageNumbers(): array
    {
        return range($this->getFirstPageNumber(), $this->getLastPageNumber());
    }
}
