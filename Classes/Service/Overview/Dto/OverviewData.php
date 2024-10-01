<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Service\Overview\Dto;

use TYPO3\CMS\Core\Pagination\ArrayPaginator;
use YoastSeoForTypo3\YoastSeo\DataProviders\OverviewDataProviderInterface;
use YoastSeoForTypo3\YoastSeo\Service\Overview\Pagination\Pagination;

class OverviewData
{
    public function __construct(
        /** @var array<string, string|int> */
        protected array $pageInformation = [],
        /** @var array<int, array<string, mixed>> */
        protected array $items = [],
        protected ArrayPaginator|null $paginator = null,
        protected Pagination|null $pagination = null,
        /** @var array<string, OverviewDataProviderInterface> */
        protected array $filters = [],
        protected OverviewDataProviderInterface|null $activeFilter = null,
        protected DataProviderRequest|null $params = null,
    ) {}

    /**
     * @return array<string, string|int>
     */
    public function getPageInformation(): array
    {
        return $this->pageInformation;
    }

    /**
     * @param array<string, string|int> $pageInformation
     */
    public function setPageInformation(array $pageInformation): OverviewData
    {
        $this->pageInformation = $pageInformation;
        return $this;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @param array<int, array<string, mixed>> $items
     */
    public function setItems(array $items): OverviewData
    {
        $this->items = $items;
        return $this;
    }

    public function getPaginator(): ?ArrayPaginator
    {
        return $this->paginator;
    }

    public function setPaginator(?ArrayPaginator $paginator): OverviewData
    {
        $this->paginator = $paginator;
        return $this;
    }

    public function getPagination(): ?Pagination
    {
        return $this->pagination;
    }

    public function setPagination(?Pagination $pagination): OverviewData
    {
        $this->pagination = $pagination;
        return $this;
    }

    /**
     * @return array<string, OverviewDataProviderInterface>
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * @param array<string, OverviewDataProviderInterface>|null $filters
     */
    public function setFilters(array|null $filters): OverviewData
    {
        $this->filters = $filters ?? [];
        return $this;
    }

    public function getActiveFilter(): ?OverviewDataProviderInterface
    {
        return $this->activeFilter;
    }

    public function setActiveFilter(?OverviewDataProviderInterface $activeFilter): OverviewData
    {
        $this->activeFilter = $activeFilter;
        return $this;
    }

    public function getParams(): ?DataProviderRequest
    {
        return $this->params;
    }

    public function setParams(?DataProviderRequest $params): OverviewData
    {
        $this->params = $params;
        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'pageInformation' => $this->pageInformation,
            'items' => $this->items,
            'paginator' => $this->paginator,
            'pagination' => $this->pagination,
            'filters' => $this->filters,
            'activeFilter' => $this->activeFilter,
            'params' => $this->params,
        ];
    }
}
