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

        return implode(PHP_EOL, $lineBuffer);
    }

}