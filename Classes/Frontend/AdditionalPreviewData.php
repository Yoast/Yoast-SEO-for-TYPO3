<?php
declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Frontend;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * This class will take care of the different providers and returns the title with
 * the highest priority
 */
class AdditionalPreviewData implements SingletonInterface
{
    /**
     * @var array
     */
    private $config;
    /**
     * @var string
     */
    private $siteTitle;

    /**
     * @var \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer
     */
    private $cObj;

    public function __construct()
    {
        $this->config = $GLOBALS['TSFE']->tmpl->setup['config.'] ?? [];
        $this->siteTitle = $GLOBALS['TSFE']->tmpl->setup['sitetitle'] ?? '';
        $this->cObj = GeneralUtility::makeInstance(ContentObjectRenderer::class);
    }

    /**
     * @param array $params
     * @param object $pObj
     */
    public function render(&$params, $pObj)
    {
        $config = $this->getPageTitlePrependAppend();
        $params['headerData']['YoastPreview'] = '<meta name="x-yoast-title-config" value="' . (string)$config['prepend'] . '|||' . (string)$config['append'] . '" />';
    }

    /**
     * Get page title prepend append
     *
     * @return array
     */
    protected function getPageTitlePrependAppend(): array
    {
        $prependAppend = ['prepend' => '', 'append' => ''];
        $siteTitle = trim($this->siteTitle);
        $pageTitleFirst = (bool)($this->config['pageTitleFirst'] ?? false);
        $pageTitleSeparator = $this->getPageTitleSeparator();
        // only show a separator if there are both site title and page title
        if ($siteTitle === '') {
            $pageTitleSeparator = '';
        } elseif (empty($pageTitleSeparator)) {
            // use the default separator if non given
            $pageTitleSeparator = ': ';
        }

        if ($pageTitleFirst) {
            $prependAppend['append'] = $pageTitleSeparator . $siteTitle;
        } else {
            $prependAppend['prepend'] = $siteTitle . $pageTitleSeparator;
        }

        return $prependAppend;
    }

    /**
     * Get page title separator
     *
     * @return string
     */
    protected function getPageTitleSeparator(): string
    {
        $pageTitleSeparator = '';
        // Check for a custom pageTitleSeparator, and perform stdWrap on it
        if (isset($this->config['pageTitleSeparator'])
            && $this->config['pageTitleSeparator'] !== '') {
            $pageTitleSeparator = $this->config['pageTitleSeparator'];

            if (isset($this->config['pageTitleSeparator.'])
                && is_array($this->config['pageTitleSeparator.'])) {
                $pageTitleSeparator = $this->cObj->stdWrap(
                    $pageTitleSeparator,
                    $this->config['pageTitleSeparator.']
                );
            } else {
                $pageTitleSeparator .= ' ';
            }
        }

        return $pageTitleSeparator;
    }
}
