<?php
namespace YoastSeoForTypo3\YoastSeo\UserFunc;

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

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * Class SitemapXml
 * @package YoastSeoForTypo3\YoastSeo\UserFunc
 */
class SitemapXml
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

    protected $privateResourcesPath;
    protected $publicResourcesPath;

    /**
     *
     */
    public function render()
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

        if (empty($sitemapConfig = GeneralUtility::_GP('tx_yoastseo_sitemap'))) {
            $this->getIndex();
        } else {
            $this->getSitemap($sitemapConfig);
        }

        $this->view->assignMultiple($this->variables);
        $this->view->setPartialRootPaths($this->partialRootPaths);
        $this->view->setTemplatePathAndFilename($this->templateFile);

        return $this->view->render();
    }

    protected function getIndex()
    {
        $this->templateFile = ExtensionManagementUtility::extPath('yoast_seo') . 'Resources/Private/Templates/SitemapXml/Index.xml';
        $sitemaps = $this->getSitemaps();

        $this->variables = array_merge(
            $this->variables,
            [
                'sitemaps' => $sitemaps
            ]
        );
    }

    /**
     * @param string $sitemapConfig
     */
    protected function getSitemap($sitemapConfig)
    {
        $tsfe = $this->getTSFE();
        $db = $this->getDb();

        $this->templateFile = ExtensionManagementUtility::extPath('yoast_seo') . 'Resources/Private/Templates/SitemapXml/List.xml';
        $docs = [];
        $sitemapSettings = $this->settings[$sitemapConfig . '.'];
        if (
            is_array($sitemapSettings) &&
            array_key_exists('table', $sitemapSettings)
        ) {
            if ($sitemapSettings['table'] === 'pages') {
                $rootPid = $sitemapSettings['rootPid'] ?: $tsfe->id;
                $where = $sitemapSettings['additionalWhere'] ?: '';

                $docs[] = $tsfe->sys_page->getPage($rootPid);
                $docs = array_filter(
                    $this->getSubPages($rootPid, $docs, $where),
                    '\YoastSeoForTypo3\YoastSeo\UserFunc\SitemapXml::filterNoIndexPages'
                );
            } else {
                $where[] = $sitemapSettings['additionalWhere'] ?: '1=1';
                $where[] = $tsfe->sys_page->enableFields($sitemapSettings['table']);
                $sortField = $sitemapSettings['sortField'] ?: 'tstamp DESC';

                $docs = $db->exec_SELECTgetRows('*', $sitemapSettings['table'], implode('', $where), '', $sortField);

                $cObject = GeneralUtility::makeInstance(ContentObjectRenderer::class);
                $typoLinkConf = [
                    'parameter' => (int)$sitemapSettings['detailPid'],
                    'forceAbsoluteUrl' => 1,
                    'useCacheHash' => !empty((bool)$sitemapSettings['useCacheHash'])
                ];

                foreach ($docs as $k => $record) {
                    if ($sitemapSettings['additionalParams']) {
                        $typoLinkConf['additionalParams'] = '&' . $sitemapSettings['additionalParams'] . '=' . $record['uid'];
                    } else {
                        $typoLinkConf['additionalParams'] = '';
                    }

                    $docs[$k]['loc'] = $cObject->typoLink_URL($typoLinkConf);
                }
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
     * @param $parentPageId
     * @param array $pages
     * @param string $additionalWhereClause
     * @param string $fields
     * @param string $sortField
     * @param bool $checkShortcuts
     * @return array
     */
    protected function getSubPages($parentPageId, array $pages = [], $additionalWhereClause = '', $fields = '*', $sortField = 'sorting', $checkShortcuts = false)
    {
        $subPages = $this->getTSFE()->sys_page->getMenu($parentPageId, $fields, $sortField, $additionalWhereClause, $checkShortcuts);
        $pages = array_merge($pages, $subPages);

        foreach ($subPages as $subPage) {
            $pages = $this->getSubPages($subPage['uid'], $pages, $additionalWhereClause, $fields, $sortField, $checkShortcuts);
        }

        return $pages;
    }

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
     * @return void
     */
    protected function getSettings()
    {
        $tsfe = $this->getTSFE();

        if (
            !empty($tsfe->tmpl->setup['plugin.']['tx_yoastseo.']['sitemap.']['config.']) &&
            is_array($tsfe->tmpl->setup['plugin.']['tx_yoastseo.']['sitemap.']['config.'])
        ) {
            return $tsfe->tmpl->setup['plugin.']['tx_yoastseo.']['sitemap.']['config.'];
        }

        return [];
    }

    /**
     * @return void
     */
    protected function getPartialRootPaths()
    {
        $tsfe = $this->getTSFE();

        if (
            !empty($tsfe->tmpl->setup['plugin.']['tx_yoastseo.']['sitemap.']['view.']['partialRootPaths.']) &&
            is_array($tsfe->tmpl->setup['plugin.']['tx_yoastseo.']['sitemap.']['view.']['partialRootPaths.'])
        ) {
            return $tsfe->tmpl->setup['plugin.']['tx_yoastseo.']['sitemap.']['view.']['partialRootPaths.'];
        }

        return [];
    }

    /**
     * @return \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController
     */
    protected function getTSFE()
    {
        return  $GLOBALS['TSFE'];
    }

    /**
     * @return \TYPO3\CMS\Core\Database\DatabaseConnection
     */
    protected function getDb()
    {
        return $GLOBALS['TYPO3_DB'];
    }

    public function filterNoIndexPages($var)
    {
        if ((int)$var['no_index'] === 1) {
            return false;
        }
        return true;
    }
}
