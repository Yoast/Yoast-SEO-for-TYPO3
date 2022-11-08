<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\StructuredData;

use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

class SiteStructuredDataProvider implements StructuredDataProviderInterface
{
    protected TypoScriptFrontendController $tsfe;
    protected PageRepository $pageRepository;
    protected SiteFinder $siteFinder;
    protected array $configuration = [];

    public function __construct(
        TypoScriptFrontendController $tsfe = null,
        PageRepository $pageRepository = null,
        SiteFinder $siteFinder = null
    ) {
        $this->tsfe = $tsfe ?? $GLOBALS['TSFE'];
        $this->pageRepository = $pageRepository ?? GeneralUtility::makeInstance(PageRepository::class);
        $this->siteFinder = $siteFinder ?? GeneralUtility::makeInstance(SiteFinder::class);
    }

    public function getData(): array
    {
        $data = [];
        if ((int)$this->tsfe->page['is_siteroot'] === 1) {
            $data[] = [
                '@context' => 'https://www.schema.org',
                '@type' => 'WebSite',
                'url' => $this->getUrl((int)$this->tsfe->page['uid']),
                'name' => $this->getName((int)$this->tsfe->page['uid']),
            ];
        }

        return $data;
    }

    protected function getUrl(int $pageId): string
    {
        if (class_exists(SiteFinder::class)) {
            $site = $this->siteFinder->getSiteByPageId($pageId);

            return (string)$site->getBase();
        }

        $cObj = GeneralUtility::makeInstance(ContentObjectRenderer::class);
        return (string)$cObj->typoLink('', ['parameter' => $pageId, 'returnLast' => 'url', 'forceAbsoluteUrl' => true]);
    }

    protected function getName(int $pageId): string
    {
        $rootPageRecord = $this->pageRepository->getPage($pageId);

        return $rootPageRecord['seo_title'] ?: $rootPageRecord['title'] ?: $this->getUrl($pageId);
    }

    public function setConfiguration(array $configuration): void
    {
        $this->configuration = $configuration;
    }
}
