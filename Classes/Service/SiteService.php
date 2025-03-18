<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Service;

use TYPO3\CMS\Core\Exception\SiteNotFoundException;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\SiteFinder;

class SiteService
{
    protected array $sites = [];

    public function __construct(
        protected SiteFinder $siteFinder
    ) {}

    public function getSiteByPageId(int $pageId): ?Site
    {
        if (isset($this->sites[$pageId])) {
            return $this->sites[$pageId];
        }

        try {
            $site = $this->siteFinder->getSiteByPageId($pageId);
            $this->sites[$pageId] = $site;
            return $site;
        } catch (SiteNotFoundException) {
            return null;
        }
    }

    public function getSiteRootPageId(int $pageUid): int
    {
        $site = $this->getSiteByPageId($pageUid);
        if ($site) {
            return $site->getRootPageId();
        }

        return 0;
    }

    public function getWebsiteTitle(int $pageUid, int $languageId): string
    {
        $site = $this->getSiteByPageId($pageUid);
        if (!$site) {
            return '';
        }

        $language = $site->getLanguageById($languageId);
        if (trim($language->getWebsiteTitle()) !== '') {
            return $language->getWebsiteTitle();
        }
        if (trim($site->getConfiguration()['websiteTitle'] ?? '') !== '') {
            return $site->getConfiguration()['websiteTitle'];
        }

        return '';
    }
}
