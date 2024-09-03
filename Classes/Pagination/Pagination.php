<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Pagination;

use TYPO3\CMS\Core\Pagination\ArrayPaginator;

/**
 * Class Pagination
 *
 * Functionality ported from the old fluid widget paginate, to support huge overviews
 * (prevent long lists of numbered links)
 * TODO: Can be removed once CMS11 support is dropped
 */
class Pagination
{
    protected ArrayPaginator $paginator;

    protected int $maximumNumberOfLinks = 15;
    protected float $displayRangeStart = 0;
    protected float $displayRangeEnd = 0;

    public function __construct(ArrayPaginator $paginator)
    {
        $this->paginator = $paginator;
        $this->calculateDisplayRange();
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
        return range($this->displayRangeStart, $this->displayRangeEnd);
    }

    public function getHasLessPages(): bool
    {
        return $this->displayRangeStart > 2;
    }

    public function getHasMorePages(): bool
    {
        return $this->displayRangeEnd + 1 < $this->paginator->getNumberOfPages();
    }

    protected function calculateDisplayRange(): void
    {
        $maximumNumberOfLinks = $this->maximumNumberOfLinks;
        $numberOfPages = $this->paginator->getNumberOfPages();
        $currentPage = $this->paginator->getCurrentPageNumber();
        if ($maximumNumberOfLinks > $numberOfPages) {
            $maximumNumberOfLinks = $numberOfPages;
        }
        $delta = floor($maximumNumberOfLinks / 2);
        $this->displayRangeStart = $currentPage - $delta;
        $this->displayRangeEnd = $currentPage + $delta - ($maximumNumberOfLinks % 2 === 0 ? 1 : 0);
        if ($this->displayRangeStart < 1) {
            $this->displayRangeEnd -= $this->displayRangeStart - 1;
        }
        if ($this->displayRangeEnd > $numberOfPages) {
            $this->displayRangeStart -= $this->displayRangeEnd - $numberOfPages;
        }
        $this->displayRangeStart = (int)max($this->displayRangeStart, 1);
        $this->displayRangeEnd = (int)min($this->displayRangeEnd, $numberOfPages);
    }
}
