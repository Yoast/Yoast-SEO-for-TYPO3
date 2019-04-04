<?php
namespace YoastSeoForTypo3\YoastSeo\Frontend;

use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

class PageTitle
{
    public function render(&$params, $pObj)
    {
        /** @var TypoScriptFrontendController $tsfe */
        $tsfe = $GLOBALS['TSFE'];

        if ($tsfe instanceof TypoScriptFrontendController) {
            $pageTitleSeparator = '';

            // check for a custom pageTitleSeparator, and perform stdWrap on it
            if (isset($tsfe->config['config']['pageTitleSeparator']) && $tsfe->config['config']['pageTitleSeparator'] !== '') {
                $pageTitleSeparator = $tsfe->config['config']['pageTitleSeparator'];

                if (isset($tsfe->config['config']['pageTitleSeparator.']) && is_array($tsfe->config['config']['pageTitleSeparator.'])) {
                    $pageTitleSeparator = $tsfe->cObj->stdWrap($pageTitleSeparator,
                        $tsfe->config['config']['pageTitleSeparator.']);
                } else {
                    $pageTitleSeparator .= ' ';
                }
            }

            $titleTagContent = $tsfe->tmpl->printTitle(
                $tsfe->altPageTitle ?: $tsfe->page['seo_title'] ?: $tsfe->page['title'],
                $tsfe->config['config']['noPageTitle'],
                $tsfe->config['config']['pageTitleFirst'],
                $pageTitleSeparator
            );
            if ($tsfe->config['config']['titleTagFunction']) {
                $titleTagContent = $tsfe->cObj->callUserFunction(
                    $tsfe->config['config']['titleTagFunction'],
                    [],
                    $titleTagContent
                );
            }
            // stdWrap around the title tag
            if (isset($tsfe->config['config']['pageTitle.']) && is_array($tsfe->config['config']['pageTitle.'])) {
                $titleTagContent = $tsfe->cObj->stdWrap($titleTagContent, $tsfe->config['config']['pageTitle.']);
            }

            $params['title'] = $titleTagContent;
        }
    }
}
