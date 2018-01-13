<?php
namespace YoastSeoForTypo3\YoastSeo\ViewHelpers;

use TYPO3\CMS\Core;
use TYPO3\CMS\Fluid;
use TYPO3\CMS\Frontend;

/**
 * Class CanonicalLinkViewHelper
 *
 * @package YoastSeoForTypo3\YoastSeo\ViewHelpers
 */
class CanonicalLinkViewHelper extends Fluid\Core\ViewHelper\AbstractTagBasedViewHelper
{

    /**
     * Name of the tag to be created by this view helper
     *
     * @var string
     */
    protected $tagName = 'link';

    /**
     * @param string $href
     *
     * @return string
     */
    public function render($href = null)
    {
        $this->tag->addAttribute('rel', 'canonical');

        if (!empty($href) && Core\Utility\GeneralUtility::isValidUrl($href)) {
            $this->tag->addAttribute('href', $href);
        } elseif ($GLOBALS['TSFE'] instanceof Frontend\Controller\TypoScriptFrontendController
            && $GLOBALS['TSFE']->contentPid > 0
        ) {
            $contentObject = Core\Utility\GeneralUtility::makeInstance(Frontend\ContentObject\ContentObjectRenderer::class);
            $content = $contentObject->typoLink_URL(
                [
                    'parameter' => '#',
                    'useCacheHash' => true,
                    'addQueryString' => true,
                    'forceAbsoluteUrl' => true,
                    'addQueryString.' => [
                        'exclude' => 'type'
                    ]
                ]
            );

            $this->tag->addAttribute('href', $content);
        }

        return $this->tag->hasAttribute('href') ? $this->tag->render() : '';
    }
}
