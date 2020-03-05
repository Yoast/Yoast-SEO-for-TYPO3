<?php
declare(strict_types=1);
namespace YoastSeoForTypo3\YoastSeo\Service;

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Type\Bitmask\Permission;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Page\PageRepository;
use YoastSeoForTypo3\YoastSeo\Utility\YoastUtility;

/**
 * Class UrlService
 * @package YoastSeoForTypo3\YoastSeo\Service
 */
class UrlService
{
    /**
     * @var int
     */
    const FE_PREVIEW_TYPE = 1480321830;

    /**
     * @var bool
     */
    protected $routeEnhancerError = false;

    /**
     * Get target url
     *
     * @param int $pageId
     * @param int $languageId
     * @param string $additionalGetVars
     * @return string
     */
    public function getTargetUrl(
        int $pageId,
        int $languageId,
        $additionalGetVars = ''
    ): string {
        $permissionClause = $this->getBackendUser()->getPagePermsClause(Permission::PAGE_SHOW);
        $pageRecord = BackendUtility::readPageAccess($pageId, $permissionClause);
        if ($pageRecord) {
            $rootLine = BackendUtility::BEgetRootLine($pageId);
            // Mount point overlay: Set new target page id and mp parameter
            $pageRepository = GeneralUtility::makeInstance(PageRepository::class);
            $finalPageIdToShow = $pageId;
            $mountPointInformation = $pageRepository->getMountPointInfo($pageId);
            if ($mountPointInformation && $mountPointInformation['overlay']) {
                // New page id
                $finalPageIdToShow = $mountPointInformation['mount_pid'];
                $additionalGetVars .= '&MP=' . $mountPointInformation['MPvar'];
            }

            if (version_compare(TYPO3_branch, '9.5', '>=')) {
                $siteFinder = GeneralUtility::makeInstance(SiteFinder::class);
                $site = $siteFinder->getSiteByPageId($finalPageIdToShow, $rootLine);
                if ($site instanceof Site) {
                    $this->checkRouteEnhancers($site);

                    $additionalQueryParams = [];
                    parse_str($additionalGetVars, $additionalQueryParams);
                    $additionalQueryParams['_language'] = $site->getLanguageById($languageId);
                    $uriToCheck = YoastUtility::fixAbsoluteUrl(
                        (string)$site->getRouter()->generateUri($finalPageIdToShow, $additionalQueryParams)
                    );

                    $uri = (string) $site->getRouter()->generateUri($site->getRootPageId(), ['type' => self::FE_PREVIEW_TYPE, 'uriToCheck' => $uriToCheck]);
                } else {
                    $uri = BackendUtility::getPreviewUrl($finalPageIdToShow, '', $rootLine, '', '', $additionalGetVars);
                }
            } else {
                $uri = '/?type=' . self::FE_PREVIEW_TYPE . '&pageIdToCheck=' . $pageId . '&languageIdToCheck=' . $languageId;
            }

            return $uri;
        }
        return '#';
    }

    /**
     * Check the route enhancers
     *
     * @param \TYPO3\CMS\Core\Site\Entity\Site $site
     */
    protected function checkRouteEnhancers(Site $site)
    {
        if (isset($site->getConfiguration()['routeEnhancers'])) {
            $typeEnhancer = $yoastTypeEnhancer = false;
            foreach ($site->getConfiguration()['routeEnhancers'] as $routeEnhancer) {
                if ($routeEnhancer['type'] === 'PageType') {
                    $typeEnhancer = true;
                    foreach ($routeEnhancer['map'] as $pageType) {
                        if ($pageType === self::FE_PREVIEW_TYPE) {
                            $yoastTypeEnhancer = true;
                        }
                    }
                }
            }
            if ($typeEnhancer === true && $yoastTypeEnhancer === false) {
                $this->routeEnhancerError = true;
            }
        }
    }

    /**
     * @return bool
     */
    public function getRouteEnhancerError(): bool
    {
        return $this->routeEnhancerError;
    }

    /**
     * @param int $type
     * @return string
     */
    public function getUrlForType($type): string
    {
        return '/?type=' . $type;
    }

    /**
     * @return \TYPO3\CMS\Core\Authentication\BackendUserAuthentication
     */
    protected function getBackendUser(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }
}
