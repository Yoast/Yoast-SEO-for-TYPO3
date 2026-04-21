<?php

/**
 * This file is part of the "yoast_seo" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\StructuredData;

use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Site\SiteFinder;
use YoastSeoForTypo3\YoastSeo\Service\Frontend\FrontendServiceInterface;

class SiteStructuredDataProvider implements StructuredDataProviderInterface
{
    /** @var array<string, mixed> */
    protected array $configuration = [];

    public function __construct(
        protected SiteFinder $siteFinder,
        protected PageRepository $pageRepository,
        protected FrontendServiceInterface $frontendService,
    ) {}

    /**
     * @return array<array<string, mixed>>
     */
    public function getData(): array
    {
        if (!$this->frontendService->isSiteRoot()) {
            return [];
        }
        return [
            [
                '@context' => 'https://schema.org',
                '@type' => 'WebSite',
                'url' => $this->getUrl($this->frontendService->getPageUid()),
                'name' => $this->getName($this->frontendService->getPageUid()),
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

    /**
     * @param array<string, mixed> $configuration
     */
    public function setConfiguration(array $configuration): void
    {
        $this->configuration = $configuration;
    }
}
