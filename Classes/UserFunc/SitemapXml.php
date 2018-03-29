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
use TYPO3\CMS\Extbase\Utility\ArrayUtility;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * Class SitemapXml
 * @package YoastSeoForTypo3\YoastSeo\UserFunc
 */
class SitemapXml {


    const DOKTYPE = 1522073831;

    /**
     * @var StandaloneView
     */
    protected $view;

    /**
     * @var Array
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
        $this->partialRootPaths[] = $this->privateResourcesPath . 'Partials/SitemapXml/List';

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
                $docs = $this->getSubPages($rootPid, $docs, $where);
            }
        }

        $this->partialRootPaths[] = $this->privateResourcesPath . 'Partials/SitemapXml/List/' . ucfirst(strtolower($sitemapConfig)) . '/';

        $this->variables = array_merge(
            $this->variables,
            [
                'config' => $sitemapConfig,
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
            !empty($tsfe->tmpl->setup['plugin.']['tx_yoastseo.']['sitemap.']) &&
            is_array($tsfe->tmpl->setup['plugin.']['tx_yoastseo.']['sitemap.'])
        ) {
            return $tsfe->tmpl->setup['plugin.']['tx_yoastseo.']['sitemap.'];
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
}