<?php
declare(strict_types=1);
namespace YoastSeoForTypo3\YoastSeo\Service;

use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Routing\RouteNotFoundException;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Utility\GeneralUtility;

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
        if (version_compare(TYPO3_branch, '9.5', '>=')) {
            return (string)$this->uriBuilder->buildUriFromRoute('ajax_yoast_preview', [
                'pageId' => $pageId, 'languageId' => $languageId, 'additionalGetVars' => urlencode($additionalGetVars)
            ]);
        }
        return $this->getUrlForType(self::FE_PREVIEW_TYPE, '&pageIdToCheck=' . $pageId . '&languageIdToCheck=' . $languageId);
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
     * Get save scores url
     *
     * @return string
     */
    public function getSaveScoresUrl()
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
    public function getUrlForType($type, $additionalGetParameters = ''): string
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
