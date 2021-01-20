<?php
declare(strict_types=1);
namespace YoastSeoForTypo3\YoastSeo\Service;

use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Routing\RouteNotFoundException;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Page\PageRepository;
use YoastSeoForTypo3\YoastSeo\Utility\YoastUtility;

/**
 * Class UrlService
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

            if (is_array($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS'][self::class]['urlToCheck'])) {
                foreach ($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS'][self::class]['urlToCheck'] as $_funcRef) {
                    $_params = [
                        'urlToCheck' => $uriToCheck,
                        'site' => $site,
                        'finalPageIdToShow' => $finalPageIdToShow,
                        'languageId' => $languageId
                    ];

                    $uriToCheck = GeneralUtility::callUserFunction($_funcRef, $_params, $this);
                }
            }
            $uri = (string)$this->uriBuilder->buildUriFromRoute('ajax_yoast_preview', [
                'uriToCheck' => $uriToCheck, 'pageId' => $finalPageIdToShow
            ]);
        } else {
            $uri = BackendUtility::getPreviewUrl($finalPageIdToShow, '', $rootLine, '', '', $additionalGetVars);
        }

        return $uri;
    }

    /**
     * Check the route enhancers
     *
     * @param \TYPO3\CMS\Core\Site\Entity\Site $site
     */
    protected function checkRouteEnhancers(Site $site): void
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
     * @param int $type
     * @param string $additionalGetParameters
     * @return string
     */
    public function getUrlForType(int $type, $additionalGetParameters = ''): string
    {
        return GeneralUtility::getIndpEnv('TYPO3_SITE_PATH') . '?type=' . $type . $additionalGetParameters;
    }

    /**
     * @return \TYPO3\CMS\Core\Authentication\BackendUserAuthentication
     */
    protected function getBackendUser(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }
}
