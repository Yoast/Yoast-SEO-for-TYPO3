<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\StructuredData;

use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Exception\SiteNotFoundException;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

class BreadcrumbStructuredDataProvider implements StructuredDataProviderInterface
{
    protected TypoScriptFrontendController $tsfe;
    protected SiteFinder $siteFinder;
    protected array $configuration;

    public function __construct(
        TypoScriptFrontendController $tsfe = null,
        SiteFinder $siteFinder = null
    ) {
        $this->tsfe = $tsfe ?? $GLOBALS['TSFE'];
        $this->siteFinder = $siteFinder ?? GeneralUtility::makeInstance(SiteFinder::class);
    }

    public function getData(): array
    {
        $data = [];

        $rootLine = $this->tsfe->rootLine ?: [];
        ksort($rootLine);

        $excludedDoktypes = $this->configuration['excludedDoktypes']
            ? GeneralUtility::intExplode(',', $this->configuration['excludedDoktypes'])
            : [];
        $breadcrumbs = [];
        $iterator = 1;
        $siteRootFound = false;
        foreach ($rootLine as $k => $page) {
            if ($page['hidden'] === 1) {
                continue;
            }
            $siteRootFound = $siteRootFound || $page['is_siteroot'];
            if ($siteRootFound && !in_array((int)$page['doktype'], $excludedDoktypes, true)) {
                $url = $this->getUrlForPage($page['uid']);
                if (!empty($url)) {
                    $breadcrumbs[] = [
                        '@type' => 'ListItem',
                        'position' => $iterator,
                        'item' => [
                            '@id' => $url,
                            'name' => $page['nav_title'] ?: $page['title']
                        ]
                    ];
                    $iterator++;
                }
            }
        }

        if (!empty($breadcrumbs)) {
            $data[] = [
                '@context' => 'https://www.schema.org',
                '@type' => 'BreadcrumbList',
                'itemListElement' => $breadcrumbs,
            ];
        }

        return (array)$data;
    }

    protected function getUrlForPage(int $pageId): string
    {
        if (class_exists(SiteFinder::class)) {
            try {
                $site = $this->siteFinder->getSiteByPageId($pageId);
                return (string)$site->getRouter()->generateUri($pageId, ['_language' => $this->getLanguage()]);
            } catch (SiteNotFoundException | \InvalidArgumentException $e) {
                return '';
            }
        }

        $cObj = GeneralUtility::makeInstance(ContentObjectRenderer::class);
        return (string)$cObj->typoLink('', ['parameter' => $pageId, 'returnLast' => 'url', 'forceAbsoluteUrl' => true]);
    }

    protected function getLanguage(): int
    {
        $context = GeneralUtility::makeInstance(Context::class);

        return (int)$context->getPropertyFromAspect('language', 'id');
    }

    public function setConfiguration(array $configuration): void
    {
        $this->configuration = $configuration;
    }
}
