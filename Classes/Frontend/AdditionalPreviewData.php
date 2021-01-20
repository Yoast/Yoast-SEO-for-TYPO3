<?php
declare(strict_types=1);
namespace YoastSeoForTypo3\YoastSeo\Frontend;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Site\Entity\SiteInterface;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use YoastSeoForTypo3\YoastSeo\Utility\YoastRequestHash;

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
     * @var \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer
     */
    private $cObj;

    public function __construct()
    {
        $this->config = $GLOBALS['TSFE']->tmpl->setup['config.'] ?? [];
        $this->cObj = GeneralUtility::makeInstance(ContentObjectRenderer::class);
    }

    /**
     * @param array $params
     * @param object $pObj
     */
    public function render(array &$params, object $pObj): void
    {
        $serverParams = $GLOBALS['TYPO3_REQUEST'] ? $GLOBALS['TYPO3_REQUEST']->getServerParams() : $_SERVER;
        if (!YoastRequestHash::isValid($serverParams)) {
            return;
        }

        $config = $this->getPageTitlePrependAppend();
        setcookie('yoast-preview-tstamp', (string)time()); // To prevent caching in for example varnish
        $params['headerData']['YoastPreview'] = '<meta name="x-yoast-title-config" value="' . $config['prepend'] . '|||' . $config['append'] . '" />';
    }

    /**
     * @return string
     */
    protected function getWebsiteTitle(): string
    {
        $request = $GLOBALS['TYPO3_REQUEST'];
        if (class_exists(SiteLanguage::class)) {
            $language = $request->getAttribute('language');
            if ($language instanceof SiteLanguage && method_exists($language, 'getWebsiteTitle') && trim($language->getWebsiteTitle())) {
                return trim($language->getWebsiteTitle());
            }
        }

        if (class_exists(SiteInterface::class)) {
            $site = $request->getAttribute('site');
            if ($site instanceof SiteInterface) {
                $siteConfig = $site->getConfiguration();

                if (isset($siteConfig['websiteTitle']) && !empty($siteConfig['websiteTitle'])) {
                    return trim($siteConfig['websiteTitle']);
                }
            }
        }
        if (!empty($GLOBALS['TSFE']->tmpl->setup['sitetitle'])) {
            return trim($GLOBALS['TSFE']->tmpl->setup['sitetitle']);
        }

        return '';
    }

    /**
     * Get page title prepend append
     *
     * @return array
     */
    protected function getPageTitlePrependAppend(): array
    {
        $prependAppend = ['prepend' => '', 'append' => ''];
        $siteTitle = $this->getWebsiteTitle();
        $pageTitleFirst = (bool)($this->config['pageTitleFirst'] ?? false);
        $pageTitleSeparator = $this->getPageTitleSeparator();
        // only show a separator if there are both site title and page title
        if (empty($siteTitle)) {
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
