<?php
declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\StructuredData;

use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use TYPO3\CMS\Frontend\Page\PageRepository;

class SiteStructuredDataProvider implements StructuredDataProviderInterface
{
    /**
     * @var TypoScriptFrontendController
     */
    protected $tsfe;

    /**
     * @var PageRepository
     */
    protected $pageRepository;

    /**
     * @var SiteFinder
     */
    protected $siteFinder;

    /**
     * SiteStructuredDataProvider constructor.
     * @param TypoScriptFrontendController|null $tsfe
     * @param PageRepository|null $pageRepository
     * @param SiteFinder|null $siteFinder
     */
    public function __construct($tsfe = null, $pageRepository = null, $siteFinder = null)
    {
        $this->setTsfe($tsfe);
        $this->setPageRepository($pageRepository);

        if (class_exists(SiteFinder::class)) {
            $this->setSiteFinder($siteFinder);
        }
    }

    /**
     * @return array
     * @throws \TYPO3\CMS\Core\Exception\SiteNotFoundException
     */
    public function getData(): array
    {
        $data = [];
        if ($this->tsfe->page['is_siteroot'] === 1) {
            $data[] = [
                '@context' => 'https://www.schema.org',
                '@type' => 'WebSite',
                'url' => $this->getUrl($this->tsfe->page['uid']),
                'name' => $this->getName($this->tsfe->page['uid']),
            ];
        }

        return (array)$data;
    }

    /**
     * @param int $pageId
     * @return string
     * @throws \TYPO3\CMS\Core\Exception\SiteNotFoundException
     */
    protected function getUrl(int $pageId): string
    {
        if (class_exists(SiteFinder::class)) {
            $site = $this->siteFinder->getSiteByPageId($pageId);

            return (string)$site->getBase();
        }

        return '';
    }

    /**
     * @param int $pageId
     * @return string
     * @throws \TYPO3\CMS\Core\Exception\SiteNotFoundException
     */
    protected function getName(int $pageId): string
    {
        if (class_exists(SiteFinder::class)) {
            $site = $this->siteFinder->getSiteByPageId($pageId);

            $rootPageId = (int)$site->getRootPageId();

            $rootPageRecord = $this->pageRepository->getPage($rootPageId);

            return $rootPageRecord['seo_title'] ?: $rootPageRecord['title'] ?: $this->getUrl($pageId);
        }

        return '';
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
     * @param PageRepository|null $pageRepository
     */
    protected function setPageRepository($pageRepository = null): void
    {
        if ($pageRepository instanceof PageRepository) {
            $this->pageRepository = $pageRepository;
        } else {
            $this->pageRepository = GeneralUtility::makeInstance(PageRepository::class);
        }
    }

    /**
     * @param SiteFinder|null $siteFinder
     */
    protected function setSiteFinder($siteFinder = null): void
    {
        if ($siteFinder instanceof SiteFinder) {
            $this->siteFinder = $siteFinder;
        } else {
            $this->siteFinder = GeneralUtility::makeInstance(SiteFinder::class);
        }
    }
}
