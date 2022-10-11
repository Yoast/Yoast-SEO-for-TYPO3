<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Controller;

use TYPO3\CMS\Backend\Routing\Exception\RouteNotFoundException;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Backend\View\BackendTemplateView;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Exception\SiteNotFoundException;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Type\Bitmask\Permission;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use YoastSeoForTypo3\YoastSeo\Pagination\ArrayPaginator;
use YoastSeoForTypo3\YoastSeo\Pagination\Pagination;
use YoastSeoForTypo3\YoastSeo\Utility\PathUtility;

class OverviewController extends ActionController
{
    protected $defaultViewObjectName = BackendTemplateView::class;

    public function listAction(int $currentPage = 1): void
    {
        $pageInformation = $this->getPageInformation();
        if ($pageInformation === null) {
            $this->view->assign('noPageSelected', true);
            return;
        }

        $this->setDocumentHeader($pageInformation);
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

        $this->view->assignMultiple([
            'items' => $items,
            'paginator' => $arrayPaginator,
            'pagination' => $pagination,
            'filters' => $filters,
            'activeFilter' => $activeFilter,
            'params' => $params,
            'subtitle' => $this->getLanguageService()->sL($currentFilter['label']),
            'description' => $currentFilter['description'],
            'link' => $currentFilter['link'],
        ]);

        GeneralUtility::makeInstance(PageRenderer::class)->addCssFile(
            PathUtility::getPublicPathToResources() . '/CSS/yoast-seo-backend.min.css'
        );
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

    protected function setDocumentHeader(array $pageInformation): void
    {
        $site = $this->getSite($pageInformation['uid']);
        if ($site === null) {
            return;
        }
        $this->view->getModuleTemplate()->getDocHeaderComponent()->setMetaInformation($pageInformation);

        $languages = $site->getAvailableLanguages($this->getBackendUser());
        if (count($languages) === 0) {
            return;
        }

        $filter = $this->request->hasArgument('filter') ? $this->request->getArgument('filter') : '';
        $languageMenu = $this->view->getModuleTemplate()->getDocHeaderComponent()->getMenuRegistry()->makeMenu();
        $languageMenu->setIdentifier('languageMenu');
        $languageMenu->setLabel(
            $this->getLanguageService()->sL('LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.language')
        );
        $returnUrl = $this->request->hasArgument('returnUrl') ? $this->request->getArgument('returnUrl') : '';
        foreach ($languages as $language) {
            $parameters = [
                'id' => $pageInformation['uid'],
                'tx_yoastseo_yoast_yoastseooverview[filter]' => $filter,
                'tx_yoastseo_yoast_yoastseooverview[language]' => $language->getLanguageId(),
                'tx_yoastseo_yoast_yoastseooverview[returnUrl]' => $returnUrl
            ];

            $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
            try {
                $url = $uriBuilder->buildUriFromRoute('yoast_YoastSeoOverview', $parameters);
            } catch (RouteNotFoundException $e) {
                $url = '';
            }

            $menuItem = $languageMenu
                ->makeMenuItem()
                ->setTitle($language->getTitle())
                ->setHref((string)$url);
            if ($this->request->hasArgument('language')
                && (int)$this->request->getArgument('language') === $language->getLanguageId()) {
                $menuItem->setActive(true);
            }
            $languageMenu->addMenuItem($menuItem);
        }
        $this->view->getModuleTemplate()->getDocHeaderComponent()->getMenuRegistry()->addMenu($languageMenu);
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
        $language = $this->request->hasArgument('language') ? (int)$this->request->getArgument('language') : 0;
        $table = 'pages';

        return [
            'language' => $language,
            'table' => $table
        ];
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
