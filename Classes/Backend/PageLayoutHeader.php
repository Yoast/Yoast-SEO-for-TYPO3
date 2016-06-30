<?php
namespace YoastSeoForTypo3\YoastSeo\Backend;


use TYPO3\CMS;

class PageLayoutHeader
{

    /**
     * @var CMS\Core\Page\PageRenderer
     */
    protected $pageRenderer;

    /**
     * Initialize the page renderer
     */
    public function __construct()
    {
        $this->pageRenderer = CMS\Core\Utility\GeneralUtility::makeInstance(CMS\Core\Page\PageRenderer::class);
    }

    /**
     * @return string
     */
    public function render()
    {
        $lineBuffer = array();

        $this->pageRenderer->addHeaderData('<!--' . __METHOD__ . '-->');
        $this->pageRenderer->loadRequireJsModule('TYPO3/CMS/YoastSeo/bundle');

        $baseUrl = '../' . \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::siteRelPath('yoast_seo');
        $this->pageRenderer->addCssFile($baseUrl . 'Resources/Public/CSS/yoast-seo.min.css', 'stylesheet', 'all', '', false, '', true);

        $lineBuffer[] = '<div id="snippet"></div><div id="output"></div>';

        return implode(PHP_EOL, $lineBuffer);
    }

}