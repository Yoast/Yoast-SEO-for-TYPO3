<?php
declare(strict_types=1);
namespace YoastSeoForTypo3\YoastSeo\Controller;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Backend\Routing\Exception\RouteNotFoundException;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Backend\View\BackendTemplateView;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\HiddenRestriction;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Localization\Locales;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;

/**
 * Class OverviewController
 */
class OverviewController extends ActionController
{
    /**
     * Backend Template Container.
     * Takes care of outer "docheader" and other stuff this module is embedded in.
     *
     * @var string
     */
    protected $defaultViewObjectName = BackendTemplateView::class;

    /**
     * @var string
     */
    protected $llPrefix = 'LLL:EXT:yoast_seo/Resources/Private/Language/BackendModuleOverview.xlf:';

    /**
     * BackendTemplateContainer
     *
     * @var BackendTemplateView
     */
    protected $view;

    /**
     * @var string
     */
    protected $activeFilter;

    /**
     * @var array
     */
    protected $currentFilter;

    /**
     * @var array
     */
    protected $filters;

    /**
     * @var PageRenderer
     */
    protected $pageRenderer;

    /**
     * @var Locales
     */
    protected $localeService;
    /**
     * @var array[]
     */
    private $MOD_MENU;

    /**
     * @param ViewInterface $view
     *
     * @return void
     */
    protected function initializeView(ViewInterface $view): void
    {
        parent::initializeView($view);

        // Early return for actions without valid view like tcaCreateAction or tcaDeleteAction
        if (!($this->view instanceof BackendTemplateView)) {
            return;
        }

        $this->makeLanguageMenu();
    }

    protected function initializeAction(): void
    {
        parent::initializeAction();

        if (!($this->localeService instanceof Locales)) {
            $this->localeService = GeneralUtility::makeInstance(Locales::class);
        }
        if (!($this->pageRenderer instanceof PageRenderer)) {
            $this->pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
        }

        $this->filters = $this->getAvailableFilters();
        $this->activeFilter = $this->getActiveFilter();

        $this->currentFilter = $this->filters[$this->activeFilter];

        $publicResourcesPath = PathUtility::getAbsoluteWebPath(ExtensionManagementUtility::extPath('yoast_seo')) . 'Resources/Public/';

        $this->pageRenderer->addCssFile(
            $publicResourcesPath . 'CSS/yoast-seo-backend.min.css'
        );
    }

    public function listAction(): void
    {
        $params = $this->getParams();
        $items = GeneralUtility::callUserFunction($this->currentFilter['dataProvider'], $params, $this) ?: [];

        $this->view->assignMultiple([
            'items' => $items,
            'filters' => $this->filters,
            'activeFilter' => $this->activeFilter,
            'params' => $params,
            'subtitle' => $this->getLanguageService()->sL($this->currentFilter['label']),
            'description' => $this->currentFilter['description'],
            'link' => $this->currentFilter['link'],
        ]);
    }

    /**
     * Returns LanguageService
     *
     * @return \TYPO3\CMS\Core\Localization\LanguageService
     */
    public function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }

    /**
     * @return array|bool
     */
    public function getAvailableFilters()
    {
        if (array_key_exists('overview_filters', $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['yoast_seo'])
            && is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['yoast_seo']['overview_filters'])
        ) {
            $params = $this->getParams();
            foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['yoast_seo']['overview_filters'] as $key => &$filter) {
                $filter['numberOfItems'] = (int)GeneralUtility::callUserFunction($filter['countProvider'], $params, $this);
            }
            return (array)$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['yoast_seo']['overview_filters'];
        }

        return false;
    }

    /**
     * @return string
     */
    protected function getActiveFilter(): string
    {
        $activeFilter = '';

        if ($this->request->hasArgument('filter')) {
            foreach ($this->filters as $k => $f) {
                if ($f['key'] === $this->request->getArgument('filter')) {
                    $activeFilter = $k;
                    break;
                }
            }
        }

        if (empty($activeFilter)) {
            reset($this->filters);
            $activeFilter = key($this->filters);
        }

        return (string)$activeFilter;
    }

    /**
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException
     */
    protected function makeLanguageMenu(): void
    {
        $lang = $this->getLanguageService();

        $qb = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('sys_language');
        $qb->getRestrictions()->removeAll()->add(GeneralUtility::makeInstance(HiddenRestriction::class));

        $rows = $qb->select('uid', 'title')
            ->from('sys_language')
            ->execute()
            ->fetchAll();

        $this->MOD_MENU = [
            'language' => [
                0 => $lang->sL('LLL:EXT:yoast_seo/Resources/Private/Language/BackendModule.xlf:defaultLanguage')
            ]
        ];

        foreach ($rows as $language) {
            if ($this->getBackendUser()->checkLanguageAccess($language['uid'])) {
                $this->MOD_MENU['language'][$language['uid']] = $language['title'];
            }
        }
        if (count($this->MOD_MENU['language']) > 1) {
            $filter = $this->request->hasArgument('filter') ? $this->request->getArgument('filter') : '';
            $lang = $this->getLanguageService();
            $languageMenu = $this->view->getModuleTemplate()->getDocHeaderComponent()->getMenuRegistry()->makeMenu();
            $languageMenu->setIdentifier('languageMenu');
            $languageMenu->setLabel($lang->sL('LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.language'));
            $returnUrl = ($this->request->hasArgument('returnUrl')) ? $this->request->getArgument('returnUrl') : '';
            foreach ($this->MOD_MENU['language'] as $key => $language) {
                $parameters = [
                    'tx_yoastseo_yoast_yoastseooverview[filter]' => $filter,
                    'tx_yoastseo_yoast_yoastseooverview[language]' => $key,
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
                    ->setTitle($language)
                    ->setHref((string)$url);
                if ($this->request->hasArgument('language') &&
                    (int)$this->request->getArgument('language') === $key) {
                    $menuItem->setActive(true);
                }
                $languageMenu->addMenuItem($menuItem);
            }
            $this->view->getModuleTemplate()->getDocHeaderComponent()->getMenuRegistry()->addMenu($languageMenu);
        }
    }

    /**
     * Returns the current BE user.
     *
     * @return \TYPO3\CMS\Core\Authentication\BackendUserAuthentication
     */
    protected function getBackendUser(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }

    /**
     * @return array
     */
    protected function getParams(): array
    {
        $language = $this->request->hasArgument('language') ? (int)$this->request->getArgument('language') : 0;
        $table = 'pages';

        return [
            'language' => $language,
            'table' => $table
        ];
    }
}
