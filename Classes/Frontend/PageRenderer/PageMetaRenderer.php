<?php
namespace YoastSeoForTypo3\YoastSeo\Frontend\PageRenderer;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\TypoScript\TemplateService;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use YoastSeoForTypo3\YoastSeo;

class PageMetaRenderer implements SingletonInterface
{

    /**
     * @param array $parameters
     *
     * @return string
     */
    public function render(array $parameters)
    {
        /**
         * Check if `config.yoast_seo` is true before any rendering takes place
         * next make sure `plugin.tx_yoastseo` is properly configured
         * `plugin.tx_yoastseo.view` is used as configuration array for FLUIDTEMPLATE
         *
         * @see https://docs.typo3.org/typo3cms/TyposcriptReference/ContentObjects/Fluidtemplate/Index.html
         *
         * The content object renderer of TSFE is used to render FLUIDTEMPLATE
         * after `plugin.tx_yoastseo.settings` is merged with `plugin.tx_yoastseo.view.settings`
         */
        if ($GLOBALS['TSFE'] instanceof TypoScriptFrontendController
            && is_array($GLOBALS['TSFE']->config)
            && ObjectAccess::getPropertyPath($GLOBALS['TSFE']->config, 'config.yoast_seo.enabled') !== null
            && $GLOBALS['TSFE']->tmpl instanceof TemplateService
            && ObjectAccess::getPropertyPath($GLOBALS['TSFE']->tmpl->setup, 'plugin.tx_yoastseo.settings') !== null
            && ObjectAccess::getPropertyPath($GLOBALS['TSFE']->tmpl->setup, 'plugin.tx_yoastseo.view') !== null
            && $GLOBALS['TSFE']->cObj instanceof ContentObjectRenderer
        ) {
            $parameters['metaTags'][] = $GLOBALS['TSFE']->cObj->cObjGetSingle(
                'FLUIDTEMPLATE',
                array_merge_recursive(
                    $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_yoastseo.']['view.'],
                    array(
                        'settings.' => $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_yoastseo.']['settings.']
                    )
                )
            );
        }
    }
}
