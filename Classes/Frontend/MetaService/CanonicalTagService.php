<?php
namespace YoastSeoForTypo3\YoastSeo\Frontend\MetaService;


use TYPO3\CMS\Core\Utility\GeneralUtility;
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
        if (array_key_exists('tx_yoastseo_canonical_url', $typoScriptFrontendController->page)
            && !empty($typoScriptFrontendController->page['tx_yoastseo_canonical_url'])
            && GeneralUtility::isValidUrl($typoScriptFrontendController->page['tx_yoastseo_canonical_url'])
        ) {
            $configuration['parameter'] = $typoScriptFrontendController->page['tx_yoastseo_canonical_url'];
        } else {
            $configuration['parameter'] = $typoScriptFrontendController->contentPid;
        }

        $configuration['returnLast'] = 'url';
        $configuration['forceAbsoluteUrl'] = true;
        $configuration['useCashHash'] = true;
        $url = $typoScriptFrontendController->cObj->typoLink_URL($configuration);

        return '<link rel="canonical" href="' . $url . '">';
    }
}