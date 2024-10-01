<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Service\Overview;

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Type\Bitmask\Permission;
use TYPO3\CMS\Extbase\Mvc\RequestInterface;
use YoastSeoForTypo3\YoastSeo\DataProviders\OverviewDataProviderInterface;
use YoastSeoForTypo3\YoastSeo\Service\Overview\Dto\DataProviderRequest;
use YoastSeoForTypo3\YoastSeo\Service\Overview\Dto\OverviewData;
use YoastSeoForTypo3\YoastSeo\Service\Overview\Pagination\PaginationService;
use YoastSeoForTypo3\YoastSeo\Traits\BackendUserTrait;

class OverviewService
{
    use BackendUserTrait;

    /** @var array<string, OverviewDataProviderInterface> */
    protected array $filters;

    /**
     * @param iterable<string, OverviewDataProviderInterface> $filters
     */
    public function __construct(
        protected PaginationService $overviewPaginationService,
        iterable $filters
    ) {
        foreach ($filters as $key => $dataProvider) {
            $this->addFilter($key, $dataProvider);
        }
    }

    public function getOverviewData(RequestInterface $request, int $currentPage, int $itemsPerPage): OverviewData
    {
        $overviewData = new OverviewData($this->getPageInformation($request));
        if (empty($overviewData->getPageInformation())) {
            return $overviewData;
        }

        $overviewData->setFilters($this->getAvailableFilters($request));
        if (empty($overviewData->getFilters())) {
            return $overviewData;
        }

        $activeFilter = $this->getActiveFilter($request);
        $items = $activeFilter->process();

        $arrayPaginator = $this->overviewPaginationService->getArrayPaginator($items, $currentPage, $itemsPerPage);

        return $overviewData->setItems($items)
            ->setPaginator($arrayPaginator)
            ->setPagination($this->overviewPaginationService->getPagination($arrayPaginator))
            ->setActiveFilter($activeFilter)
            ->setParams($this->getDataProviderRequest($request));
    }

    /**
     * @return array<string, OverviewDataProviderInterface>|null
     */
    public function getAvailableFilters(RequestInterface $request): ?array
    {
        if ($this->filters === []) {
            return null;
        }

        $dataProviderRequest = $this->getDataProviderRequest($request);
        foreach ($this->filters as $dataProvider) {
            $dataProvider->initialize($dataProviderRequest);
        }

        return $this->filters;
    }

    protected function getActiveFilter(RequestInterface $request): OverviewDataProviderInterface
    {
        if ($this->filters === []) {
            throw new \RuntimeException('No filters available');
        }

        if ($request->hasArgument('filter')) {
            $activeFilter = $request->getArgument('filter');
            if (is_string($activeFilter) && isset($this->filters[$activeFilter])) {
                return $this->filters[$activeFilter];
            }
        }

        return current($this->filters);
    }

    protected function getDataProviderRequest(RequestInterface $request): DataProviderRequest
    {
        return new DataProviderRequest(
            (int)($request->getQueryParams()['id'] ?? 0),
            (int)($request->getQueryParams()['tx_yoastseo_yoast_yoastseooverview']['language'] ?? $request->getQueryParams()['language'] ?? 0),
            'pages'
        );
    }

    /**
     * @return array<string, string|int>
     */
    protected function getPageInformation(RequestInterface $request): array
    {
        $id = (int)($request->getQueryParams()['id'] ?? 0);
        if ($id === 0) {
            return [];
        }
        $pageInformation = BackendUtility::readPageAccess(
            $id,
            $this->getBackendUser()->getPagePermsClause(Permission::PAGE_SHOW)
        );
        return is_array($pageInformation) ? $pageInformation : [];
    }

    protected function addFilter(string $key, OverviewDataProviderInterface $dataProvider): void
    {
        $this->filters[$key] = $dataProvider;
    }
}
