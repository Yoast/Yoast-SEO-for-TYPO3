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
        $configuration['returnLast'] = 'url';
        $configuration['parameter'] =  '#';
        $configuration['forceAbsoluteUrl'] = true;
        $configuration['useCashHash'] = true;
        $url = $typoScriptFrontendController->cObj->typoLink_URL($configuration);

        return '<link rel="canonical" href="' . $url . '">';
    }
}