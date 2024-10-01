<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Frontend;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use YoastSeoForTypo3\YoastSeo\Service\YoastRequestService;

class AdditionalPreviewData implements SingletonInterface
{
    /** @var array<string, mixed> */
    protected array $config;

    public function __construct(
        protected YoastRequestService $yoastRequestService
    ) {
        $this->config = $GLOBALS['TSFE']->tmpl->setup['config.'] ?? [];
    }

    /**
     * @param array<string, mixed> $params
     */
    public function render(array &$params, object $pObj): void
    {
        $serverParams = $GLOBALS['TYPO3_REQUEST'] ? $GLOBALS['TYPO3_REQUEST']->getServerParams() : $_SERVER;
        if (!$this->yoastRequestService->isValidRequest($serverParams)) {
            return;
        }

        $config = $this->getPageTitlePrependAppend();
        setcookie('yoast-preview-tstamp', (string)time()); // To prevent caching in for example varnish
        $params['headerData']['YoastPreview'] = '<meta name="x-yoast-title-config" value="' . $config['prepend'] . '|||' . $config['append'] . '" />';
    }

    protected function getWebsiteTitle(): string
    {
        $request = $GLOBALS['TYPO3_REQUEST'];
        $language = $request->getAttribute('language');
        if ($language instanceof SiteLanguage && !empty($language->getWebsiteTitle())) {
            return trim($language->getWebsiteTitle());
        }
        $site = $request->getAttribute('site');
        if ($site instanceof Site && !empty($site->getConfiguration()['websiteTitle'] ?? '')) {
            return trim($site->getConfiguration()['websiteTitle']);
        }

        if (!empty($GLOBALS['TSFE']->tmpl->setup['sitetitle'] ?? '')) {
            return trim($GLOBALS['TSFE']->tmpl->setup['sitetitle']);
        }

        return '';
    }

    /**
     * @return string[]
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

    protected function getPageTitleSeparator(): string
    {
        if (!isset($this->config['pageTitleSeparator']) || $this->config['pageTitleSeparator'] === '') {
            return '';
        }

        if (is_array($this->config['pageTitleSeparator.'] ?? null)) {
            return (string)GeneralUtility::makeInstance(ContentObjectRenderer::class)
                ->stdWrap($this->config['pageTitleSeparator'], $this->config['pageTitleSeparator.']);
        }

        return $this->config['pageTitleSeparator'] . ' ';
    }
}
