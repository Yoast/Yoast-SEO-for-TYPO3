<?php
declare(strict_types=1);
namespace YoastSeoForTypo3\YoastSeo\Service;

use Psr\Http\Message\UriInterface;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Exception\SiteNotFoundException;
use TYPO3\CMS\Core\Routing\RouteNotFoundException;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use YoastSeoForTypo3\YoastSeo\Utility\YoastUtility;

/**
 * Class UrlService
 */
class UrlService implements SingletonInterface
{
    /**
     * @var \TYPO3\CMS\Backend\Routing\UriBuilder
     */
    protected $uriBuilder;

    /**
     * UrlService constructor.
     */
    public function __construct()
    {
        $this->uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
    }

    /**
     * Get target url
     *
     * @param int $pageId
     * @param int $languageId
     * @param string $additionalGetVars
     * @return string
     */
    public function getPreviewUrl(
        int $pageId,
        int $languageId,
        $additionalGetVars = ''
    ): string {
        return (string)$this->uriBuilder->buildUriFromRoute('ajax_yoast_preview', [
            'pageId' => $pageId, 'languageId' => $languageId, 'additionalGetVars' => urlencode($additionalGetVars)
        ]);
    }

    /**
     * @param int    $pageId
     * @param int    $languageId
     * @param string $additionalGetVars
     * @return string
     */
    public function getUriToCheck(int $pageId, int $languageId, string $additionalGetVars): string
    {
        $this->checkMountpoint($pageId, $additionalGetVars);
        $rootLine = $this->getRootLine($pageId);
        $site = $this->getSite($pageId, $rootLine);

        if ($site !== null) {
            $uriToCheck = YoastUtility::fixAbsoluteUrl(
                (string)$this->generateUri($site, $pageId, $languageId, $additionalGetVars)
            );

            if (is_array($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS'][self::class]['urlToCheck'])) {
                foreach ($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS'][self::class]['urlToCheck'] as $_funcRef) {
                    $_params = [
                        'urlToCheck' => $uriToCheck,
                        'site' => $site,
                        'finalPageIdToShow' => $pageId,
                        'languageId' => $languageId
                    ];

                    $uriToCheck = GeneralUtility::callUserFunction($_funcRef, $_params, $this);
                }
            }
            return $uriToCheck;
        }
        return '';
    }

    /**
     * @param int $pageId
     * @param     $additionalGetVars
     */
    public function checkMountPoint(int &$pageId, &$additionalGetVars): void
    {
        $pageRepository = $this->getPageRepository();
        $mountPointInformation = $pageRepository->getMountPointInfo($pageId);
        if ($mountPointInformation && $mountPointInformation['overlay']) {
            // New page id
            $pageId = $mountPointInformation['mount_pid'];
            $additionalGetVars .= '&MP=' . $mountPointInformation['MPvar'];
        }
    }

    /**
     * @param int $pageId
     * @return array
     */
    public function getRootLine(int $pageId): array
    {
        return BackendUtility::BEgetRootLine($pageId);
    }

    /**
     * @param int   $pageId
     * @param array $rootLine
     * @return \TYPO3\CMS\Core\Site\Entity\Site|null
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

    /**
     * @param \TYPO3\CMS\Core\Site\Entity\Site $site
     * @param int                              $pageId
     * @param int                              $languageId
     * @param string                           $additionalGetVars
     * @return \Psr\Http\Message\UriInterface
     * @throws \TYPO3\CMS\Core\Routing\InvalidRouteArgumentsException
     */
    public function generateUri(Site $site, int $pageId, int $languageId, $additionalGetVars = ''): UriInterface
    {
        $additionalQueryParams = [];
        parse_str($additionalGetVars, $additionalQueryParams);
        $additionalQueryParams['_language'] = $site->getLanguageById($languageId);
        return $site->getRouter()->generateUri($pageId, $additionalQueryParams);
    }

    /**
     * @return \TYPO3\CMS\Core\Domain\Repository\PageRepository|\TYPO3\CMS\Frontend\Page\PageRepository
     */
    protected function getPageRepository()
    {
        if (class_exists(PageRepository::class)) {
            return GeneralUtility::makeInstance(PageRepository::class);
        }
        return GeneralUtility::makeInstance(\TYPO3\CMS\Frontend\Page\PageRepository::class);
    }

    /**
     * Get save scores url
     *
     * @return string
     */
    public function getSaveScoresUrl(): string
    {
        try {
            return (string)$this->uriBuilder->buildUriFromRoute('ajax_yoast_save_scores');
        } catch (RouteNotFoundException $e) {
            return '';
        }
    }

    /**
     * @return string
     */
    public function getProminentWordsUrl(): string
    {
        try {
            return (string)$this->uriBuilder->buildUriFromRoute('ajax_yoast_prominent_words');
        } catch (RouteNotFoundException $e) {
            return '';
        }
    }

    /**
     * @return \TYPO3\CMS\Core\Authentication\BackendUserAuthentication
     */
    protected function getBackendUser(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }
}
