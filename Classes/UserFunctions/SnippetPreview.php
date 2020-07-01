<?php
namespace YoastSeoForTypo3\YoastSeo\UserFunctions;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use YoastSeoForTypo3\YoastSeo\Service\PreviewService;

/**
 * Class SnippetPreview
 */
class SnippetPreview
{
    /**
     * Render function
     *
     * @return string
     */
    public function render(): string
    {
        $additionalGetVars = urldecode($_GET['additionalGetVars']);
        $cObj = GeneralUtility::makeInstance(ContentObjectRenderer::class);
        $uriToCheck = $cObj->typolink_URL([
            'parameter' => (int)$_GET['pageIdToCheck'],
            'forceAbsoluteUrl' => 1,
            'additionalParams' => $additionalGetVars . '&L=' . (int)$_GET['languageIdToCheck'],
            'linkAccessRestrictedPages' => 1,
        ]);

        $previewService = GeneralUtility::makeInstance(PreviewService::class);
        return $previewService->getPreviewData(
            $uriToCheck,
            (int)$_GET['pageIdToCheck'],
            $GLOBALS['TSFE']->config['config'] ?? [],
            $GLOBALS['TSFE']->tmpl->setup['sitetitle'] ?? ''
        );
    }
}
