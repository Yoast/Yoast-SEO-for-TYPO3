<?php
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

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Backend\View\BackendTemplateView;
use TYPO3\CMS\Core\Localization\Locales;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;

/**
 * Class OverviewController
 * @package YoastSeoForTypo3\YoastSeo\Controller
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
     * @param ViewInterface $view
     *
     * @return void
     */
    protected function initializeView(ViewInterface $view)
    {
        parent::initializeView($view);

        // Early return for actions without valid view like tcaCreateAction or tcaDeleteAction
        if (!($this->view instanceof BackendTemplateView)) {
            return;
        }

        $this->makeLanguageMenu();
    }

    protected function initializeAction()
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

        $publicResourcesPath = ExtensionManagementUtility::extRelPath('yoast_seo') . 'Resources/Public/';

        $this->pageRenderer->addCssFile(
            $publicResourcesPath . 'CSS/yoast-seo-backend.min.css'
        );
    }

    public function listAction()
    {
        $params = $this->getParams();
        $items = GeneralUtility::callUserFunction($this->currentFilter['dataProvider'], $params, $this) ?: [];

        $this->view->assignMultiple([
            'items' => $items,
            'filters' => $this->filters,
            'activeFilter' => $this->activeFilter,
            'params' => $params,
            'subtitle' => $this->getLanguageService()->sL($this->currentFilter['label']),
        ]);
    }

    /**
     * Returns LanguageService
     *
     * @return \TYPO3\CMS\Lang\LanguageService
     */
    public function getLanguageService()
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
    protected function getActiveFilter()
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

        return $activeFilter;
    }

    /**
     * Make the LanguageMenu
     *
     * @return void
     */
    protected function makeLanguageMenu()
    {
        $lang = $this->getLanguageService();
        $pageId = 0;
        if ($this->request->hasArgument('id')) {
            $pageId = $this->request->getArgument('id');
        } elseif ((int)GeneralUtility::_GET('id')) {
            $pageId = (int)GeneralUtility::_GET('id');
        }
        $this->MOD_MENU = [
            'language' => [
                0 => $lang->sL('LLL:EXT:yoast_seo/Resources/Private/Language/BackendModule.xlf:defaultLanguage')
            ]
        ];

        $rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
            'uid, title',
            'sys_language',
            'hidden=0',
            '',
            ''
        );
        foreach ($rows as $lRow) {
            if ($this->getBackendUser()->checkLanguageAccess($lRow['uid'])) {
                $this->MOD_MENU['language'][$lRow['uid']] = $lRow['title'];
            }
        }
        if (count($this->MOD_MENU['language']) > 1) {
            $filter = $this->request->hasArgument('filter') ? $this->request->getArgument('filter') : '';
            $lang = $this->getLanguageService();
            $languageMenu = $this->view->getModuleTemplate()->getDocHeaderComponent()->getMenuRegistry()->makeMenu();
            $languageMenu->setIdentifier('languageMenu');
            $languageMenu->setLabel($lang->sL('LLL:EXT:lang/locallang_general.xlf:LGL.language', true));
            $returnUrl = ($this->request->hasArgument('returnUrl')) ? $this->request->getArgument('returnUrl') : '';
            foreach ($this->MOD_MENU['language'] as $key => $language) {
                $parameters = array(
                    'tx_yoastseo_yoast_yoastseooverview[filter]' => $filter,
                    'tx_yoastseo_yoast_yoastseooverview[language]' => $key,
                    'tx_yoastseo_yoast_yoastseooverview[returnUrl]' => $returnUrl
                );
                $url = BackendUtility::getModuleUrl('yoast_YoastSeoOverview', $parameters);
                $menuItem = $languageMenu
                    ->makeMenuItem()
                    ->setTitle($language)
                    ->setHref($url);
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
    protected function getBackendUser()
    {
        return $GLOBALS['BE_USER'];
    }

    /**
     * @return array
     */
    protected function getParams()
    {
        $language = $this->request->hasArgument('language') ? (int)$this->request->getArgument('language') : 0;
        $table = $language ? 'pages_language_overlay' : 'pages';

        return [
            'language' => $language,
            'table' => $table
        ];
    }
}
