<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Pagination;

/**
 * Class ArrayPaginator
 * @TODO This is a simplified port of the ArrayPaginator class from TYPO3 CMS10 and up, this is only needed for CMS9 and can be removed when CMS9 support is dropped
 */
class ArrayPaginator
{
    /**
     * @var int
     */
    protected $numberOfPages = 1;

    /**
     * @var int
     */
    private $currentPageNumber = 1;

    /**
     * @var int
     */
    private $itemsPerPage = 10;

    /**
     * @var array
     */
    private $items;

    /**
     * @var array
     */
    private $paginatedItems = [];

    public function __construct(
        array $items,
        int $currentPageNumber = 1,
        int $itemsPerPage = 10
    ) {
        $this->items = $items;
        $this->setCurrentPageNumber($currentPageNumber);
        $this->setItemsPerPage($itemsPerPage);

        $this->updateInternalState();
    }

    /**
     * @return iterable|array
     */
    public function getPaginatedItems(): iterable
    {
        return $this->paginatedItems;
    }

    protected function updatePaginatedItems(int $itemsPerPage, int $offset): void
    {
        $this->paginatedItems = array_slice($this->items, $offset, $itemsPerPage);
    }

    protected function getTotalAmountOfItems(): int
    {
        return count($this->items);
    }

    public function getNumberOfPages(): int
    {
        return $this->numberOfPages;
    }

    public function getCurrentPageNumber(): int
    {
        return $this->currentPageNumber;
    }

    /**
     * This method is the heart of the pagination. It updates all internal params and then calls the
     * {@see updatePaginatedItems} method which must update the set of paginated items.
     */
    protected function updateInternalState(): void
    {
        $offset = (int)($this->itemsPerPage * ($this->currentPageNumber - 1));
        $totalAmountOfItems = $this->getTotalAmountOfItems();

        /*
         * If the total amount of items is zero, then the number of pages is mathematically zero as
         * well. As that looks strange in the frontend, the number of pages is forced to be at least
         * one.
         */
        $this->numberOfPages = max(1, (int)ceil($totalAmountOfItems / $this->itemsPerPage));

        /*
         * To prevent empty results in case the given current page number exceeds the maximum number
         * of pages, we set the current page number to the last page and update the internal state
         * with this value again. Such situation should in the first place be prevented by not allowing
         * those values to be passed, e.g. by using the "max" attribute in the view. However there are
         * valid cases. For example when a user deletes a record while the pagination is already visible
         * to another user with, until then, a valid "max" value. Passing invalid values unintentionally
         * should therefore just silently be resolved.
         */
        if ($this->currentPageNumber > $this->numberOfPages) {
            $this->currentPageNumber = $this->numberOfPages;
            $this->updateInternalState();
            return;
        }

        $this->updatePaginatedItems($this->itemsPerPage, $offset);
    }

    protected function setItemsPerPage(int $itemsPerPage): void
    {
        if ($itemsPerPage < 1) {
            throw new \InvalidArgumentException(
                'Argument $itemsPerPage must be greater than 0',
                1573061766
            );
        }

        $this->itemsPerPage = $itemsPerPage;
    }

    protected function setCurrentPageNumber(int $currentPageNumber): void
    {
        if ($currentPageNumber < 1) {
            throw new \InvalidArgumentException(
                'Argument $currentPageNumber must be greater than 0',
                1573047338
            );
        }

        $this->currentPageNumber = $currentPageNumber;
    }
}
