<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Service;

use Psr\Http\Message\UriInterface;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Exception\SiteNotFoundException;
use TYPO3\CMS\Core\Routing\RouteNotFoundException;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use YoastSeoForTypo3\YoastSeo\Utility\YoastUtility;

class UrlService implements SingletonInterface
{
    public function __construct(
        protected UriBuilder $uriBuilder
    ) {
    }

    public function getPreviewUrl(
        int $pageId,
        int $languageId,
        string $additionalGetVars = ''
    ): string {
        return (string)$this->uriBuilder->buildUriFromRoute('ajax_yoast_preview', [
            'pageId' => $pageId,
            'languageId' => $languageId,
            'additionalGetVars' => urlencode($additionalGetVars)
        ]);
    }

    public function getUriToCheck(int $pageId, int $languageId, string $additionalGetVars): string
    {
        $this->checkMountpoint($pageId, $additionalGetVars);
        $rootLine = $this->getRootLine($pageId);
        $site = $this->getSite($pageId, $rootLine);

        if ($site !== null) {
            $uriToCheck = YoastUtility::fixAbsoluteUrl(
                (string)$this->generateUri($site, $pageId, $languageId, $additionalGetVars)
            );

            foreach ($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS'][self::class]['urlToCheck'] ?? [] as $_funcRef) {
                $_params = [
                    'urlToCheck' => $uriToCheck,
                    'site' => $site,
                    'finalPageIdToShow' => $pageId,
                    'languageId' => $languageId
                ];

                $uriToCheck = GeneralUtility::callUserFunction($_funcRef, $_params, $this);
            }
            return $uriToCheck;
        }
        return '';
    }

    public function checkMountPoint(int &$pageId, string &$additionalGetVars): void
    {
        $pageRepository = GeneralUtility::makeInstance(PageRepository::class);
        $mountPointInformation = $pageRepository->getMountPointInfo($pageId);
        if ($mountPointInformation && $mountPointInformation['overlay']) {
            // New page id
            $pageId = $mountPointInformation['mount_pid'];
            $additionalGetVars .= '&MP=' . $mountPointInformation['MPvar'];
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function getRootLine(int $pageId): array
    {
        return BackendUtility::BEgetRootLine($pageId);
    }

    /**
     * @param array<string, mixed> $rootLine
     */
    public function getSite(int $pageId, array $rootLine): ?Site
    {
        $siteFinder = GeneralUtility::makeInstance(SiteFinder::class);
        try {
            return $siteFinder->getSiteByPageId($pageId, $rootLine);
        } catch (SiteNotFoundException $e) {
            return null;
        }
    }

    public function generateUri(Site $site, int $pageId, int $languageId, string $additionalGetVars = ''): UriInterface
    {
        $additionalQueryParams = [];
        $additionalGetVars = rawurldecode($additionalGetVars);
        parse_str($additionalGetVars, $additionalQueryParams);
        $additionalQueryParams['_language'] = $site->getLanguageById($languageId);
        return $site->getRouter()->generateUri($pageId, $additionalQueryParams);
    }

    public function getSaveScoresUrl(): string
    {
        try {
            return (string)$this->uriBuilder->buildUriFromRoute('ajax_yoast_save_scores');
        } catch (RouteNotFoundException) {
            return '';
        }
    }

    public function getProminentWordsUrl(): string
    {
        try {
            return (string)$this->uriBuilder->buildUriFromRoute('ajax_yoast_prominent_words');
        } catch (RouteNotFoundException) {
            return '';
        }
    }
}
