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
    /**
     * @var TypoScriptFrontendController
     */
    protected $tsfe;

    /**
     * @var SiteFinder
     */
    protected $siteFinder;

    /**
     * @var array
     */
    protected $configuration;

    /**
     * BreadcrumbStructuredDataProvider constructor.
     * @param null $tsfe
     * @param null $siteFinder
     */
    public function __construct($tsfe = null, $siteFinder = null)
    {
        $this->setTsfe($tsfe);

        if (class_exists(SiteFinder::class)) {
            $this->setSiteFinder($siteFinder);
        }
    }

    /**
     * @return array
     * @throws \TYPO3\CMS\Core\Context\Exception\AspectNotFoundException
     */
    public function getData(): array
    {
        $data = [];

        $rootLine = $this->tsfe->rootLine ?: [];
        ksort($rootLine);

        $excludedDoktypes = $this->configuration['excludedDoktypes'] ? GeneralUtility::intExplode(',', $this->configuration['excludedDoktypes']) : [];
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

    /**
     * @param TypoScriptFrontendController|null $tsfe
     */
    protected function setTsfe(?TypoScriptFrontendController $tsfe): void
    {
        if ($tsfe instanceof TypoScriptFrontendController) {
            $this->tsfe = $tsfe;
        } else {
            $this->tsfe = $GLOBALS['TSFE'];
        }
    }

    /**
     * @param int $pageId
     * @return string
     * @throws \TYPO3\CMS\Core\Context\Exception\AspectNotFoundException
     */
    protected function getUrlForPage(int $pageId): string
    {
        if (class_exists(SiteFinder::class)) {
            try {
                $site = $this->siteFinder->getSiteByPageId((int)$pageId);
            } catch (SiteNotFoundException $e) {
                return '';
            }

            return (string)$site->getRouter()->generateUri($pageId, ['_language' => $this->getLanguage()]);
        }

        $cObj = GeneralUtility::makeInstance(ContentObjectRenderer::class);
        return (string)$cObj->typoLink('', ['parameter' => $pageId, 'returnLast' => 'url', 'forceAbsoluteUrl' => true]);
    }

    /**
     * @param SiteFinder|null $siteFinder
     */
    protected function setSiteFinder(?SiteFinder $siteFinder): void
    {
        if ($siteFinder instanceof SiteFinder) {
            $this->siteFinder = $siteFinder;
        } else {
            $this->siteFinder = GeneralUtility::makeInstance(SiteFinder::class);
        }
    }

    /**
     * @return int
     * @throws \TYPO3\CMS\Core\Context\Exception\AspectNotFoundException
     */
    protected function getLanguage(): int
    {
        $context = GeneralUtility::makeInstance(Context::class);

        return (int)$context->getPropertyFromAspect('language', 'id');
    }

    /**
     * @param array $configuration
     */
    public function setConfiguration(array $configuration): void
    {
        $this->configuration = $configuration;
    }
}
