<?php
namespace YoastSeoForTypo3\YoastSeo\Frontend\PageRenderer;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

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
        $config = isset($GLOBALS['TSFE']->tmpl->setup) ? $GLOBALS['TSFE']->tmpl->setup : [];
        if (is_array($config)
            && !(bool)$GLOBALS['TSFE']->page['tx_yoastseo_dont_use']
            && isset(
                $config['plugin.']['tx_yoastseo.']['settings.'],
                $config['plugin.']['tx_yoastseo.']['view.'],
                $config['config.']['yoast_seo.']['enabled']
            )
            && (int) $config['config.']['yoast_seo.']['enabled'] !== 0
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
