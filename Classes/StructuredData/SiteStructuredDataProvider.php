<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\StructuredData;

use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

class SiteStructuredDataProvider implements StructuredDataProviderInterface
{
    protected array $configuration = [];

    public function __construct(
        protected SiteFinder $siteFinder,
        protected PageRepository $pageRepository,
    ) {}

    public function getData(): array
    {
        if ((int)$this->getTyposcriptFrontendController()->page['is_siteroot'] !== 1) {
            return [];
        }
        return [
            [
                '@context' => 'https://www.schema.org',
                '@type' => 'WebSite',
                'url' => $this->getUrl((int)$this->getTyposcriptFrontendController()->page['uid']),
                'name' => $this->getName((int)$this->getTyposcriptFrontendController()->page['uid']),
            ],
        ];
    }

    protected function getUrl(int $pageId): string
    {
        $site = $this->siteFinder->getSiteByPageId($pageId);
        return (string)$site->getBase();
    }

    protected function getName(int $pageId): string
    {
        $rootPageRecord = $this->pageRepository->getPage($pageId);
        return $rootPageRecord['seo_title'] ?: $rootPageRecord['title'] ?: $this->getUrl($pageId);
    }

    protected function getTyposcriptFrontendController(): TypoScriptFrontendController
    {
        return $GLOBALS['TSFE'];
    }

    public function setConfiguration(array $configuration): void
    {
        $this->configuration = $configuration;
    }
}
