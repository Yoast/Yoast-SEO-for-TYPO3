<?php
namespace YoastSeoForTypo3\YoastSeo\UserFunctions;

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

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryHelper;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\Page\PageRepository;

/**
 * Class XmlSitemap
 */
class XmlSitemap
{
    const DOKTYPE = 1522073831;

    /**
     * @var StandaloneView
     */
    protected $view;

    /**
     * @var array
     */
    protected $settings;

    /**
     * @var string
     */
    protected $templateFile;

    /**
     * @var array
     */
    protected $variables = [];

    /**
     * @var array
     */
    protected $partialRootPaths = [];

    /**
     * @var string
     */
    protected $privateResourcesPath;

    /**
     * @var string
     */
    protected $publicResourcesPath;

    /**
     * Render function
     */
    public function render()
    {
        $this->initialize();

        if (empty($sitemapConfig = GeneralUtility::_GP('tx_yoastseo_sitemap'))) {
            $this->getIndex();
        } else {
            $this->getSitemap($sitemapConfig);
        }

        return $this->renderView();
    }

    /**
     * Initialize
     */
    protected function initialize()
    {
        $this->view = GeneralUtility::makeInstance(StandaloneView::class);
        $this->view->setFormat('xml');
        $this->view->getRequest()->setControllerExtensionName('yoast_seo');
        $this->privateResourcesPath = ExtensionManagementUtility::extPath('yoast_seo') . 'Resources/Private/';
        $this->publicResourcesPath = ExtensionManagementUtility::siteRelPath('yoast_seo') . 'Resources/Public/';
        $this->templateFile = $this->privateResourcesPath . 'Templates/SitemapXml/Index.xml';
        $this->partialRootPaths = $this->getPartialRootPaths();

        $this->variables = [
            'xslPath' => $this->publicResourcesPath . 'CSS',
            'type' => self::DOKTYPE
        ];

        $this->settings = $this->getSettings();
    }

    /**
     * Render view
     *
     * @return string
     */
    protected function renderView()
    {
        $this->view->assignMultiple($this->variables);
        $this->view->setPartialRootPaths($this->partialRootPaths);
        $this->view->setTemplatePathAndFilename($this->templateFile);

        return $this->view->render();
    }

    /**
     * Get index
     */
    protected function getIndex()
    {
        $this->templateFile = $this->privateResourcesPath . 'Templates/SitemapXml/Index.xml';
        $sitemaps = $this->getSitemaps();

        $this->variables = array_merge(
            $this->variables,
            [
                'sitemaps' => $sitemaps
            ]
        );
    }

    /**
     * Get sitemap
     *
     * @param string $sitemapConfig
     */
    protected function getSitemap($sitemapConfig)
    {
        $this->templateFile = $this->privateResourcesPath . 'Templates/SitemapXml/List.xml';

        $docs = [];
        $sitemapSettings = $this->settings[$sitemapConfig . '.'];

        if (is_array($sitemapSettings)) {
            $this->renderContentObjectsInFields($sitemapSettings);
        }

        if (is_array($sitemapSettings) && array_key_exists('table', $sitemapSettings)) {
            if ($sitemapSettings['table'] === 'pages') {
                $docs = $this->getPages($sitemapSettings);
            } else {
                $docs = $this->getRecords($sitemapSettings);
            }
        }

        $this->variables = array_merge(
            $this->variables,
            [
                'config' => $sitemapConfig,
                'partialName' => $sitemapSettings['partialName'] ?: $sitemapConfig,
                'docs' => $docs
            ]
        );
    }

    /**
     * Get pages
     *
     * @param array $sitemapSettings
     * @return mixed
     */
    protected function getPages($sitemapSettings)
    {
        $rootPid = $sitemapSettings['rootPid'] ?: $this->getTSFE()->id;

        $cObj = GeneralUtility::makeInstance(ContentObjectRenderer::class);
        $treeList = $cObj->getTreeList(-$rootPid, 99);
        $treeListArray = GeneralUtility::intExplode(',', $treeList);

        if (class_exists(ConnectionPool::class)) {
            $pages = $this->queryBuilderPages($treeListArray, $sitemapSettings);
        } else {
            $pages = $this->databaseConnectionPages($treeListArray, $sitemapSettings);
        }

        $pageRepository = GeneralUtility::makeInstance(PageRepository::class);
        return $pageRepository->getPagesOverlay($pages);
    }

    /**
     * Get pages with queryBuilder
     *
     * @param array $treeListArray
     * @param array $sitemapSettings
     * @return array
     */
    protected function queryBuilderPages($treeListArray, $sitemapSettings)
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('pages');

        $constraints = [
            $queryBuilder->expr()->in('uid', $treeListArray),
            $queryBuilder->expr()->neq('no_index', 1)
        ];

        if (!empty($sitemapSettings['additionalWhere'])) {
            $constraints[] = QueryHelper::stripLogicalOperatorPrefix($sitemapSettings['additionalWhere']);
        }
        return $queryBuilder->select('*')
            ->from('pages')
            ->where(...$constraints)
            ->orderBy('uid', 'ASC')
            ->execute()
            ->fetchAll();
    }

    /**
     * Get pages with (old) DatabaseConnection
     *
     * @param array $treeListArray
     * @param array $sitemapSettings
     * @return array
     */
    protected function databaseConnectionPages($treeListArray, $sitemapSettings)
    {
        return $this->getDb()->exec_SELECTgetRows(
            '*',
            'pages',
            'uid IN(' . implode(',', $treeListArray) . ') AND no_index=0 '
            . ($sitemapSettings['additionalWhere'] ?: ''),
            '',
            'uid ASC'
        );
    }

    /**
     * Get records
     *
     * @param array $sitemapSettings
     * @return array
     */
    protected function getRecords($sitemapSettings)
    {
        $where = $sitemapSettings['additionalWhere'] ?: '1=1';
        $where .= $this->getTSFE()->sys_page->enableFields($sitemapSettings['table']);
        $sortField = $sitemapSettings['sortField'] ?: 'tstamp DESC';

        $records = $this->getDb()->exec_SELECTgetRows(
            '*',
            $sitemapSettings['table'],
            $where,
            '',
            $sortField
        );

        $cObject = GeneralUtility::makeInstance(ContentObjectRenderer::class);
        $typoLinkConf = [
            'parameter' => (int)$sitemapSettings['detailPid'],
            'forceAbsoluteUrl' => 1,
            'useCacheHash' => !empty((bool)$sitemapSettings['useCacheHash'])
        ];

        $docs = [];
        foreach ($records as $record) {
            if ($sitemapSettings['additionalParams']) {
                $typoLinkConf['additionalParams'] = '&' . $sitemapSettings['additionalParams'] . '=' . $record['uid'];
            } else {
                $typoLinkConf['additionalParams'] = '';
            }
            $recordUrl = $cObject->typoLink_URL($typoLinkConf);
            if (!empty($recordUrl)) {
                $record['loc'] = $recordUrl;
                $docs[] = $record;
            }
        }
        return $docs;
    }

    /**
     * Get sitemaps
     *
     * @return array
     */
    protected function getSitemaps()
    {
        $sitemaps = [];
        foreach ($this->settings as $type => $configuration) {
            if (is_array($configuration)) {
                $key = rtrim($type, '.');

                $sitemaps[] = $key;
            }
        }
        return $sitemaps;
    }

    /**
     * Get settings
     *
     * @return array
     */
    protected function getSettings()
    {
        $tsfe = $this->getTSFE();

        if (!empty($tsfe->tmpl->setup['plugin.']['tx_yoastseo.']['sitemap.']['config.']) &&
            is_array($tsfe->tmpl->setup['plugin.']['tx_yoastseo.']['sitemap.']['config.'])
        ) {
            return $tsfe->tmpl->setup['plugin.']['tx_yoastseo.']['sitemap.']['config.'];
        }

        return [];
    }

    /**
     * Get partial root paths
     *
     * @return array
     */
    protected function getPartialRootPaths()
    {
        $tsfe = $this->getTSFE();

        if (!empty($tsfe->tmpl->setup['plugin.']['tx_yoastseo.']['sitemap.']['view.']['partialRootPaths.']) &&
            is_array($tsfe->tmpl->setup['plugin.']['tx_yoastseo.']['sitemap.']['view.']['partialRootPaths.'])
        ) {
            return $tsfe->tmpl->setup['plugin.']['tx_yoastseo.']['sitemap.']['view.']['partialRootPaths.'];
        }

        return [];
    }

    /**
     * Get typoscript frontend controller
     *
     * @return \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController
     */
    protected function getTSFE()
    {
        return $GLOBALS['TSFE'];
    }

    /**
     * Get database connection
     *
     * @return \TYPO3\CMS\Core\Database\DatabaseConnection
     */
    protected function getDb()
    {
        return $GLOBALS['TYPO3_DB'];
    }

    /**
     * Loop through a config array and render the cObjects in it
     *
     * @param array $fields
     */
    protected function renderContentObjectsInFields(&$fields)
    {
        // Look for keys ending with "."
        $cObjConfKeys = preg_grep('/\.$/', array_keys($fields));

        // If no cObj conf was found, return early
        if (!count($cObjConfKeys)) {
            return;
        }

        /** @var ContentObjectRenderer $contentObjectRenderer */
        $contentObjectRenderer = GeneralUtility::makeInstance(ContentObjectRenderer::class);

        foreach ($cObjConfKeys as $index) {
            // Main property (same as key without the "." at the end)
            $name = substr($index, 0, -1);
            $cType = $fields[$name];

            // Render cObject and save it in place of the cType
            $fields[$name] = $contentObjectRenderer->cObjGetSingle($cType, $fields[$index]);

            // Remove the cObj conf from the array
            unset($fields[$index]);
        }
    }
}
