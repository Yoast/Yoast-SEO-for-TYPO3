<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Controller;

use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Exception\SiteNotFoundException;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Type\Bitmask\Permission;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use YoastSeoForTypo3\YoastSeo\Pagination\ArrayPaginator;
use YoastSeoForTypo3\YoastSeo\Pagination\Pagination;

class OverviewController extends AbstractBackendController
{
    public function listAction(int $currentPage = 1): ResponseInterface
    {
        $overviewData = $this->getOverviewData($currentPage) + ['action' => 'list'];
        $moduleTemplate = $this->getModuleTemplate();
        if (!isset($overviewData['pageInformation'])) {
            return $this->returnResponse($overviewData, $moduleTemplate);
        }

        $moduleTemplate->getDocHeaderComponent()->setMetaInformation($overviewData['pageInformation']);
        $languageMenuItems = $this->getLanguageMenuItems($overviewData['pageInformation']);
        if (is_array($languageMenuItems)) {
            $languageMenu = $moduleTemplate->getDocHeaderComponent()->getMenuRegistry()->makeMenu();
            $languageMenu->setIdentifier('languageMenu');
            $languageMenu->setLabel(
                $this->getLanguageService()->sL('LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.language')
            );
            foreach ($languageMenuItems as $languageMenuItem) {
                $menuItem = $languageMenu
                    ->makeMenuItem()
                    ->setTitle($languageMenuItem['title'])
                    ->setHref($languageMenuItem['href'])
                    ->setActive($languageMenuItem['active']);
                $languageMenu->addMenuItem($menuItem);
            }
            $moduleTemplate->getDocHeaderComponent()->getMenuRegistry()->addMenu($languageMenu);
        }
        return $this->returnResponse($overviewData, $moduleTemplate);
    }

    public function legacyAction(int $currentPage = 1): void
    {
        $overviewData = $this->getOverviewData($currentPage) + ['action' => 'legacy'];
        if (isset($overviewData['pageInformation'])) {
            $overviewData['languageMenuItems'] = $this->getLanguageMenuItems($overviewData['pageInformation']);
        }
        $this->view->assignMultiple($overviewData);
    }

    protected function getOverviewData(int $currentPage): array
    {
        $pageInformation = $this->getPageInformation();
        if (!isset($pageInformation['uid'])) {
            return ['noPageSelected' => true];
        }

        $filters = $this->getAvailableFilters();
        $activeFilter = $this->getActiveFilter($filters);
        $currentFilter = $filters[$activeFilter];

        $params = $this->getParams();
        $items = GeneralUtility::callUserFunction($currentFilter['dataProvider'], $params, $this) ?: [];

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
            'params' => $params,
            'subtitle' => $this->getLanguageService()->sL($currentFilter['label']),
            'description' => $currentFilter['description'],
            'link' => $currentFilter['link'],
        ];
    }

    public function getAvailableFilters(): ?array
    {
        if (array_key_exists('overview_filters', $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['yoast_seo'])
            && is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['yoast_seo']['overview_filters'])
        ) {
            $params = $this->getParams();
            foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['yoast_seo']['overview_filters'] as &$filter) {
                $filter['numberOfItems'] = (int)GeneralUtility::callUserFunction(
                    $filter['countProvider'],
                    $params,
                    $this
                );
            }
            return (array)$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['yoast_seo']['overview_filters'];
        }

        return null;
    }

    protected function getActiveFilter(array $filters): string
    {
        $activeFilter = '';

        if ($this->request->hasArgument('filter')) {
            foreach ($filters as $k => $f) {
                if ($f['key'] === $this->request->getArgument('filter')) {
                    $activeFilter = $k;
                    break;
                }
            }
        }

        if (empty($activeFilter)) {
            $activeFilter = key($filters);
        }

        return (string)$activeFilter;
    }

    protected function getLanguageMenuItems(array $pageInformation): ?array
    {
        $site = $this->getSite($pageInformation['uid']);
        if ($site === null) {
            return null;
        }

        $languages = $site->getAvailableLanguages($this->getBackendUser());
        if (count($languages) === 0) {
            return null;
        }

        $arguments = $this->getArguments();

        $filter = $arguments['filter'] ?? '';
        $returnUrl = $arguments['returnUrl'] ?? '';
        $items = [];
        foreach ($languages as $language) {
            $url = $this->uriBuilder
                ->reset()
                ->setTargetPageUid($pageInformation['uid'])
                ->setArguments([
                    'tx_yoastseo_yoast_yoastseooverview' => [
                        'filter' => $filter,
                        'language' => $language->getLanguageId(),
                        'returnUrl' => $returnUrl,
                        'controller' => 'Overview'
                    ]
                ])
                ->build();
            $items[] = [
                'title' => $language->getTitle(),
                'href' => $url,
                'active' => (int)($arguments['language'] ?? 0) === $language->getLanguageId()
            ];
        }
        return $items;
    }

    protected function getPageInformation(): ?array
    {
        $id = GeneralUtility::_GET('id');
        $pageInformation = BackendUtility::readPageAccess(
            $id,
            $this->getBackendUser()->getPagePermsClause(Permission::PAGE_SHOW)
        );
        return is_array($pageInformation) ? $pageInformation : null;
    }

    protected function getSite(int $pageUid): ?Site
    {
        $siteFinder = GeneralUtility::makeInstance(SiteFinder::class);
        try {
            return $siteFinder->getSiteByPageId($pageUid);
        } catch (SiteNotFoundException $e) {
            return null;
        }
    }

    protected function getParams(): array
    {
        return [
            'language' => $this->getArguments()['language'] ?? 0,
            'table' => 'pages'
        ];
    }

    protected function getArguments(): array
    {
        return $this->request->getArguments()['tx_yoastseo_yoast_yoastseooverview'] ?? $this->request->getArguments();
    }

    public function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }

    protected function getBackendUser(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }
}
