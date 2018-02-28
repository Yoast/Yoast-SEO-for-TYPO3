<?php
namespace YoastSeoForTypo3\YoastSeo\Frontend\PageRenderer;

use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

class PageMetaRenderer implements SingletonInterface
{

    /**
     * @param array $parameters
     * @param PageRenderer $pageRenderer
     * @return string
     */
    public function render(array $parameters, &$pageRenderer)
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
                $GLOBALS['TSFE']->config['config']['yoast_seo.']['enabled']
            )
            && (int) $GLOBALS['TSFE']->config['config']['yoast_seo.']['enabled'] !== 0
            && $GLOBALS['TSFE']->cObj instanceof ContentObjectRenderer
        ) {
            $tagsToRender = [];
            $tagsArray = $this->getUniqueTagsFromConfig();
            foreach ($tagsArray as $tag => $v) {
                if ($content = $this->getTagToRender($tag)) {
                    $key = $tag;
                    if (preg_match('/\<meta (name|property)="([a-z0-9:_]*)"/', $content, $matches)) {
                        $key = $matches[1] . '|' . $matches[2];
                    }
                    $tagsToRender[$key] = $content;
                }
            }
            $metaTags = array_filter($parameters['metaTags'], function ($metaTag) use ($tagsToRender) {
                if (preg_match('/\<meta (name|property)="([a-z0-9:_]*)"/', $metaTag, $matches)) {
                    if (array_key_exists($matches[1] . '|' . $matches[2], $tagsToRender)) {
                        return false;
                    }
                }
                return true;
            });
            $parameters['metaTags'] = array_merge($metaTags, $tagsToRender);
        }
    }

    /**
     * @return array
     */
    protected function getYoastTagsTypoScript()
    {
        return $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_yoastseo.']['view.']['variables.'] ?: [];
    }

    /**
     * @param string $tag
     * @param array $config
     * @return string
     */
    public function getTagToRender($tag, array $config = [])
    {
        if (empty($config)) {
            $config = $this->getYoastTagsTypoScript();
        }

        return (string)$GLOBALS['TSFE']->cObj->cObjGetSingle($config[$tag], $config[$tag . '.']);
    }

    /**
     * @param array $config
     * @return array
     */
    public function getUniqueTagsFromConfig(array $config = [])
    {
        if (empty($config)) {
            $config = $this->getYoastTagsTypoScript();
        }
        $tags = array_filter(
            $config,
            function ($k) {
                if (preg_match('/\.+$/', $k)) {
                    return false;
                }
                return true;
            },
            ARRAY_FILTER_USE_KEY
        );

        return $tags;
    }
}
