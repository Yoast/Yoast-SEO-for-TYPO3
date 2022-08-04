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
    /**
     * @var TypoScriptFrontendController
     */
    protected TypoScriptFrontendController $tsfe;

    /**
     * @var PageRepository
     */
    protected $pageRepository;

    /**
     * @var SiteFinder
     */
    protected SiteFinder $siteFinder;

    /**
     * @var array
     */
    protected array $configuration = [];

    /**
     * SiteStructuredDataProvider constructor.
     *
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
     * @throws \TYPO3\CMS\Core\Exception\SiteNotFoundException
     * @return array
     */
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

    /**
     * @param int $pageId
     * @throws \TYPO3\CMS\Core\Exception\SiteNotFoundException
     * @return string
     */
    protected function getUrl(int $pageId): string
    {
        if (class_exists(SiteFinder::class)) {
            $site = $this->siteFinder->getSiteByPageId($pageId);

            return (string)$site->getBase();
        }

        $cObj = GeneralUtility::makeInstance(ContentObjectRenderer::class);
        return (string)$cObj->typoLink('', ['parameter' => $pageId, 'returnLast' => 'url', 'forceAbsoluteUrl' => true]);
    }

    /**
     * @param int $pageId
     * @throws \TYPO3\CMS\Core\Exception\SiteNotFoundException
     * @return string
     */
    protected function getName(int $pageId): string
    {
        $rootPageRecord = $this->pageRepository->getPage($pageId);

        return $rootPageRecord['seo_title'] ?: $rootPageRecord['title'] ?: $this->getUrl($pageId);
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
     * @param PageRepository|\TYPO3\CMS\Frontend\Page\PageRepository|null $pageRepository
     */
    protected function setPageRepository($pageRepository): void
    {
        if (class_exists(PageRepository::class)) {
            if ($pageRepository instanceof PageRepository) {
                $this->pageRepository = $pageRepository;
            } else {
                $this->pageRepository = GeneralUtility::makeInstance(PageRepository::class);
            }
        } elseif ($pageRepository instanceof \TYPO3\CMS\Frontend\Page\PageRepository) {
            $this->pageRepository = $pageRepository;
        } else {
            $this->pageRepository = GeneralUtility::makeInstance(\TYPO3\CMS\Frontend\Page\PageRepository::class);
        }
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
     * @param array $configuration
     */
    public function setConfiguration(array $configuration): void
    {
        $this->configuration = $configuration;
    }
}
