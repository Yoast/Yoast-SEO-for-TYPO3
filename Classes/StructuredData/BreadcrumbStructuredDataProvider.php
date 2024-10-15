<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\StructuredData;

use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Exception\SiteNotFoundException;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

class BreadcrumbStructuredDataProvider implements StructuredDataProviderInterface
{
    /** @var array<string, mixed> */
    protected array $configuration;

    public function __construct(
        protected SiteFinder $siteFinder,
        protected Context $context,
    ) {}

    /**
     * @return array<array<string, mixed>>
     */
    public function getData(): array
    {
        $breadcrumbs = [];
        $excludedDoktypes = $this->getExcludedDoktypes();
        $iterator = 1;
        $siteRootFound = false;
        foreach ($this->getRootLine() as $page) {
            if ($page['hidden'] === 1) {
                continue;
            }
            $siteRootFound = $siteRootFound || $page['is_siteroot'];
            if (!$siteRootFound || in_array((int)$page['doktype'], $excludedDoktypes, true)) {
                continue;
            }
            $url = $this->getUrlForPage($page['uid']);
            if (empty($url)) {
                continue;
            }
            $breadcrumbs[] = [
                '@type' => 'ListItem',
                'position' => $iterator,
                'item' => [
                    '@id' => $url,
                    'name' => $page['nav_title'] ?: $page['title'],
                ],
            ];
            $iterator++;
        }

        return $this->getBreadcrumbList($breadcrumbs);
    }

    /**
     * @param array<array<string, mixed>> $breadcrumbs
     * @return array<array<string, mixed>>
     */
    protected function getBreadcrumbList(array $breadcrumbs): array
    {
        if (empty($breadcrumbs)) {
            return [];
        }

        return [
            [
                '@context' => 'https://www.schema.org',
                '@type' => 'BreadcrumbList',
                'itemListElement' => $breadcrumbs,
            ],
        ];
    }

    /**
     * @return int[]
     */
    protected function getExcludedDoktypes(): array
    {
        if (!empty($this->configuration['excludedDoktypes'] ?? '')) {
            return GeneralUtility::intExplode(',', $this->configuration['excludedDoktypes']);
        }
        return [];
    }

    /**
     * @return array<array<string, mixed>>
     */
    protected function getRootLine(): array
    {
        $rootLine = $this->getTyposcriptFrontendController()->rootLine ?: [];
        ksort($rootLine);
        return $rootLine;
    }

    protected function getUrlForPage(int $pageId): string
    {
        try {
            $site = $this->siteFinder->getSiteByPageId($pageId);
            return (string)$site->getRouter()->generateUri(
                $pageId, ['_language' => (int)$this->context->getPropertyFromAspect('language', 'id')]
            );
        } catch (SiteNotFoundException|\InvalidArgumentException) {
            return '';
        }
    }

    protected function getTyposcriptFrontendController(): TypoScriptFrontendController
    {
        return $GLOBALS['TSFE'];
    }

    /**
     * @param array<string, mixed> $configuration
     */
    public function setConfiguration(array $configuration): void
    {
        $this->configuration = $configuration;
    }
}
