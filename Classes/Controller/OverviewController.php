<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Controller;

use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Pagination\ArrayPaginator;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use YoastSeoForTypo3\YoastSeo\Backend\Overview\DataProviderRequest;
use YoastSeoForTypo3\YoastSeo\Backend\Overview\LanguageMenu\LanguageMenuFactory;
use YoastSeoForTypo3\YoastSeo\DataProviders\OverviewDataProviderInterface;
use YoastSeoForTypo3\YoastSeo\Pagination\Pagination;

class OverviewController extends AbstractBackendController
{
    /** @var array<string, OverviewDataProviderInterface> */
    protected array $filters;

    /**
     * @param iterable<string, OverviewDataProviderInterface> $filters
     */
    public function __construct(
        protected LanguageMenuFactory $languageMenuFactory,
        iterable $filters
    ) {
        foreach ($filters as $key => $dataProvider) {
            $this->addFilter($key, $dataProvider);
        }
    }

    public function listAction(int $currentPage = 1): ResponseInterface
    {
        $overviewData = $this->getOverviewData($currentPage) + ['action' => 'list'];
        $moduleTemplate = $this->getModuleTemplate();
        if (!isset($overviewData['pageInformation'])) {
            return $this->returnResponse('Overview/List', $overviewData, $moduleTemplate);
        }

        $moduleTemplate->getDocHeaderComponent()->setMetaInformation($overviewData['pageInformation']);
        $languageMenu = $this->languageMenuFactory->create(
            $this->request,
            $moduleTemplate,
            $overviewData['pageInformation']['uid'] ?? 0
        );
        if ($languageMenu !== null) {
            $moduleTemplate->getDocHeaderComponent()->getMenuRegistry()->addMenu($languageMenu);
        }

        return $this->returnResponse('Overview/List', $overviewData, $moduleTemplate);
    }

    /**
     * @return array<string, mixed>
     */
    protected function getOverviewData(int $currentPage): array
    {
        $pageInformation = $this->getPageInformation();
        if (!isset($pageInformation['uid'])) {
            return ['noPageSelected' => true];
        }

        $filters = $this->getAvailableFilters();
        if ($filters === null) {
            return [];
        }

        $activeFilter = $this->getActiveFilter();
        $items = $activeFilter->process();

        $arrayPaginator = GeneralUtility::makeInstance(
            ArrayPaginator::class,
            $items,
            $currentPage,
            (int)$this->settings['itemsPerPage']
        );
        $pagination = GeneralUtility::makeInstance(Pagination::class, $arrayPaginator);

        return [
            'pageInformation' => $pageInformation,
            'items' => $items,
            'paginator' => $arrayPaginator,
            'pagination' => $pagination,
            'filters' => $filters,
            'activeFilter' => $activeFilter,
            'params' => $this->getDataProviderRequest(),
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getAvailableFilters(): ?array
    {
        if ($this->filters === []) {
            return null;
        }

        $dataProviderRequest = $this->getDataProviderRequest();
        foreach ($this->filters as $dataProvider) {
            $dataProvider->initialize($dataProviderRequest);
        }

        return $this->filters;
    }

    protected function getActiveFilter(): OverviewDataProviderInterface
    {
        if ($this->filters === []) {
            throw new \RuntimeException('No filters available');
        }

        if ($this->request->hasArgument('filter')) {
            $activeFilter = $this->request->getArgument('filter');
            if (is_string($activeFilter) && isset($this->filters[$activeFilter])) {
                return $this->filters[$activeFilter];
            }
        }

        return current($this->filters);
    }

    protected function getDataProviderRequest(): DataProviderRequest
    {
        return new DataProviderRequest(
            (int)($this->request->getQueryParams()['id'] ?? 0),
            (int)($this->request->getQueryParams()['tx_yoastseo_yoast_yoastseooverview']['language'] ?? $this->request->getQueryParams()['language'] ?? 0),
            'pages'
        );
    }

    protected function addFilter(string $key, OverviewDataProviderInterface $dataProvider): void
    {
        $this->filters[$key] = $dataProvider;
    }
}
