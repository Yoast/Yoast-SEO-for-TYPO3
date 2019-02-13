<?php
declare(strict_types = 1);

namespace YoastSeoForTypo3\YoastSeo\StructuredData;

use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
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
     * @throws \TYPO3\CMS\Core\Exception\SiteNotFoundException
     * @throws \TYPO3\CMS\Core\Routing\InvalidRouteArgumentsException
     */
    public function getData(): array
    {
        $data = [];

        $rootLine = $this->tsfe->rootLine;
        ksort($rootLine);

        $breadcrumbs = []; $iterator = 1;
        foreach ($rootLine as $k => $page) {
            $breadcrumbs[] = [
                '@type' => 'ListItem',
                'position' => $iterator,
                'item' => [
                    '@id' => $this->getUrlForPage($page['uid']),
                    'name' => $page['nav_title'] ?: $page['title']
                ]
            ];
            $iterator++;
        }

        if (!empty($breadcrumbs)) {
            $data = [
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
    protected function setTsfe($tsfe = null): void
    {
        if ($tsfe instanceof TypoScriptFrontendController) {
            $this->tsfe = $tsfe;
        } else {
            $this->tsfe = $GLOBALS['TSFE'];
        }
    }

    /**
     * @param $pageId
     * @return string
     * @throws \TYPO3\CMS\Core\Exception\SiteNotFoundException
     * @throws \TYPO3\CMS\Core\Routing\InvalidRouteArgumentsException
     */
    protected function getUrlForPage($pageId): string
    {
        if (class_exists(SiteFinder::class)) {
            $site = $this->siteFinder->getSiteByPageId($pageId);

            return (string)$site->getRouter()->generateUri($pageId, []);
        }

        return '';
    }

    /**
     * @param $siteFinder
     */
    protected function setSiteFinder($siteFinder): void
    {
        if ($siteFinder instanceof SiteFinder) {
            $this->siteFinder = $siteFinder;
        } else {
            $this->siteFinder = GeneralUtility::makeInstance(SiteFinder::class);
        }
    }
}
