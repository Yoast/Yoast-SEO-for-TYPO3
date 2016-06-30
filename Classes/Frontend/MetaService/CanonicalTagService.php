<?php
namespace YoastSeoForTypo3\YoastSeo\Frontend\MetaService;


use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

class CanonicalTagService implements TagRendererServiceInterface
{

    /**
     * @param TypoScriptFrontendController $typoScriptFrontendController
     *
     * @return string
     */
    public function render(TypoScriptFrontendController $typoScriptFrontendController)
    {
        return '<link rel="canonical">';
    }
}