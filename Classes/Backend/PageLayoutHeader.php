<?php
namespace YoastSeoForTypo3\YoastSeo\Backend;

use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS;
use YoastSeoForTypo3\YoastSeo\Utility\JsonConfigUtility;
use YoastSeoForTypo3\YoastSeo\Utility\YoastUtility;

class PageLayoutHeader
{

    /**
     * @var int
     */
    const FE_PREVIEW_TYPE = 1480321830;

    /**
     * @var string
     */
    const APP_TRANSLATION_FILE_PATTERN = 'EXT:yoast_seo/Resources/Private/Language/wordpress-seo-%s.json';

    /**
     * @var array
     */
    protected $configuration = array(
        'translations' => array(
            'availableLocales' => array(),
            'languageKeyToLocaleMapping' => array()
        ),
        'menuActions' => array(),
        'previewDomain' => null,
        'previewUrlTemplate' => '',
        'viewSettings' => array()
    );

    /**
     * @var CMS\Core\Localization\Locales
     */
    protected $localeService;

    /**
     * @var CMS\Core\Page\PageRenderer
     */
    protected $pageRenderer;

    /**
     * Route enhancer error
     *
     * @var bool
     */
    protected $routeEnhancerError = false;

    /**
     * Initialize the page renderer
     */
    public function __construct()
    {
        if (array_key_exists('yoast_seo', $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'])
            && is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['yoast_seo'])
        ) {
            ArrayUtility::mergeRecursiveWithOverrule(
                $this->configuration,
                $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['yoast_seo']
            );
        }

        $this->localeService = CMS\Core\Utility\GeneralUtility::makeInstance(CMS\Core\Localization\Locales::class);
        $this->pageRenderer = CMS\Core\Utility\GeneralUtility::makeInstance(CMS\Core\Page\PageRenderer::class);
    }

    /**
     * @return string
     */
    public function render()
    {
        $moduleData = CMS\Backend\Utility\BackendUtility::getModuleData(['language'], [], 'web_layout');
        $pageId = (int)$_GET['id'];

        if (!$GLOBALS['BE_USER'] instanceof CMS\Core\Authentication\BackendUserAuthentication ||
            !$GLOBALS['BE_USER']->check('modules', 'yoast_YoastSeoDashboard')) {
            return '';
        }

        if ((bool)$GLOBALS['BE_USER']->uc['hideYoastInPageModule']) {
            return '';
        }

        /** @var CMS\Backend\Controller\PageLayoutController $pageLayoutController */
        $pageLayoutController = $GLOBALS['SOBE'];

        $currentPage = null;
        $focusKeyword = '';
        $previewDataUrl = '';
        $recordId = $pageId;
        $tableName = 'pages';
        $targetElementId = uniqid('_YoastSEO_panel_');
        $publicResourcesPath = CMS\Core\Utility\PathUtility::getAbsoluteWebPath(CMS\Core\Utility\ExtensionManagementUtility::extPath('yoast_seo')) . 'Resources/Public/';
        if ($pageLayoutController instanceof CMS\Backend\Controller\PageLayoutController
            && $pageId > 0
            && (int)$moduleData['language'] === 0
        ) {
            $currentPage = CMS\Backend\Utility\BackendUtility::getRecord(
                'pages',
                $pageId
            );
        } elseif ($pageLayoutController instanceof CMS\Backend\Controller\PageLayoutController
            && $pageId > 0
            && (int)$moduleData['language'] > 0
        ) {
            $overlayRecords = CMS\Backend\Utility\BackendUtility::getRecordLocalization(
                'pages',
                $pageId,
                (int)$moduleData['language']
            );

            if (is_array($overlayRecords) && array_key_exists(0, $overlayRecords) && is_array($overlayRecords[0])) {
                $recordId = $overlayRecords[0]['uid'];
                $currentPage = $overlayRecords[0];
            }
        }

        if (!YoastUtility::snippetPreviewEnabled(
            $pageId,
            $currentPage
        )
        ) {
            return '';
        }

        if (\is_array($currentPage)) {
            $focusKeyword = YoastUtility::getFocusKeywordOfPage($recordId, $tableName);

            $previewDataUrl = $this->getTargetUrl($pageId, (int)$moduleData['language']);
        }

        $allowedDoktypes = YoastUtility::getAllowedDoktypes();
        if (is_array($currentPage) &&
            array_key_exists('doktype', $currentPage) &&
            in_array((int)$currentPage['doktype'], $allowedDoktypes, true)
        ) {
            $interfaceLocale = $this->getInterfaceLocale();

            if ($interfaceLocale !== null
                && ($translationFilePath = sprintf(
                    static::APP_TRANSLATION_FILE_PATTERN,
                    $interfaceLocale
                )) !== false
                && ($translationFilePath = CMS\Core\Utility\GeneralUtility::getFileAbsFileName(
                    $translationFilePath
                )) !== false
                && file_exists($translationFilePath)
            ) {
                $this->pageRenderer->addJsInlineCode(
                    md5($translationFilePath),
                    'var tx_yoast_seo = tx_yoast_seo || {};'
                    . ' tx_yoast_seo.translations = '
                    . file_get_contents($translationFilePath)
                    . ';'
                );
            }

            $labelBad = $GLOBALS['LANG']->sL('LLL:EXT:yoast_seo/Resources/Private/Language/BackendModule.xlf:labelBad');
            $labelOk = $GLOBALS['LANG']->sL('LLL:EXT:yoast_seo/Resources/Private/Language/BackendModule.xlf:labelOk');
            $labelGood = $GLOBALS['LANG']->sL('LLL:EXT:yoast_seo/Resources/Private/Language/BackendModule.xlf:labelGood');

            $config = [
                'snippetPreview' => [
                    'previewUrl' => $previewDataUrl,
                ],
                'isCornerstoneContent' => (bool)$currentPage['tx_yoastseo_cornerstone'],
                'focusKeyphrase' => [
                    'keyword' => (string)$currentPage['tx_yoastseo_focuskeyword'],
                    'synonyms' => (string)$currentPage['tx_yoastseo_focuskeyword_synonyms'],
                ]
            ];
            $jsonConfigUtility = GeneralUtility::makeInstance(JsonConfigUtility::class);
            $jsonConfigUtility->addConfig($config);

            $this->pageRenderer->addJsInlineCode('yoast-json-config', $jsonConfigUtility->render());

            $this->pageRenderer->addJsInlineCode(
                'YoastSEO settings',
                'var tx_yoast_seo = tx_yoast_seo || {};'
                . 'var tx_yoast_scores = new Array();'
                . 'tx_yoast_scores["bad"] = "' . $labelBad . '";'
                . 'tx_yoast_scores["ok"] = "' . $labelOk . '";'
                . 'tx_yoast_scores["good"] = "' . $labelGood . '";'
                . ' tx_yoast_seo.settings = '
                . json_encode(
                    array(
                        'focusKeyword' => $focusKeyword,
                        'preview' => $previewDataUrl,
                        'recordId' => $recordId,
                        'recordTable' => $tableName,
                        'cornerstone' => $currentPage['tx_yoastseo_cornerstone'],
                        'targetElementId' => $targetElementId,
                        'editable' => 0,
                        'disableSlug' => 1,
                        'tableName' => 'pages',
                        'vanillaUid' => $pageId,
                        'languageId' => (int)$moduleData['language']
                    )
                )
                . ';'
            );

            $this->pageRenderer->addRequireJsConfiguration(
                array(
                    'paths' => array(
                        'YoastSEO' => $publicResourcesPath . 'JavaScript/'
                    )
                )
            );

//            $this->pageRenderer->loadRequireJsModule('YoastSEO/dist/plugin');
            $this->pageRenderer->addJsInlineCode(
                'yoastseo-webpack-plugin',
                '(function () {
                            var s = document.createElement("script");
                            s.async=true;
                            s.src="https://localhost:3333/typo3conf/ext/yoast_seo/Resources/Public/JavaScript/dist/plugin.js";
                            document.querySelector("head").appendChild(s);
                        }());'
            );

            $this->pageRenderer->addCssFile(
                $publicResourcesPath . 'CSS/yoast-seo.min.css'
            );

            $uriBuilder = GeneralUtility::makeInstance(CMS\Backend\Routing\UriBuilder::class);
            $returnUrl = $uriBuilder->buildUriFromRoute('web_layout', ['id' => (int)$pageLayoutController->id]);

            $urlParameters = [
                'edit' => [
                    'pages' => [
                        $recordId => 'edit'
                    ]
                ],
                'overrideVals' => [
                    'pages' => [
                        'sys_language_uid' => (int)$moduleData['language']
                    ]
                ],
                'switchToYoast' => 1,
                'returnUrl' => $returnUrl
            ];
            $url = $uriBuilder->buildUriFromRoute('record_edit', $urlParameters);

            $returnHtml = '
                <div class="yoast-snippet-header">
                    <div class="yoast-snippet-header-icons btn-group btn-group-sm">
                        <a href="#" class="yoast-collapse" data-collapse-target="' . $targetElementId . '">
                            <span class="t3js-icon icon icon-size-small icon-state-default icon-actions-document-open"
                                data-identifier="actions-document-open">
                                <span class="icon-markup">
                                    <img src="/typo3/sysext/core/Resources/Public/Icons/T3Icons/actions/actions-move-down.svg" width="16" height="16">
                                </span>
                            </span>
                        </a>
                        <a href="' . $url . '" title="">
                            <span class="t3js-icon icon icon-size-small icon-state-default icon-actions-document-open"
                                data-identifier="actions-document-open">
                                <span class="icon-markup">
                                    <img src="/typo3/sysext/core/Resources/Public/Icons/T3Icons/actions/actions-document-open.svg" width="16" height="16">
                                </span>
                            </span>
                        </a>
                    </div>
                    <div class="yoast-snippet-header-label">Yoast SEO</div>
                </div>';

            if ($this->routeEnhancerError === false) {
                $returnHtml .= '
                <input id="focusKeyword" style="display: none" />
                <div id="' . $targetElementId . '" class="t3-grid-cell yoastSeo yoastSeo--small" data-yoast-snippetpreview>
                    <!-- ' . $targetElementId . ' -->
                    <div class="spinner">
                      <div class="bounce bounce1"></div>
                      <div class="bounce bounce2"></div>
                      <div class="bounce bounce3"></div>
                    </div>
                </div>';
            } else {
                $returnHtml .= '
                <div class="t3-grid-cell yoast yoastSeo yoastSeo--small">
                    <div class="callout callout-warning callout-body">
                        It seems that you have configured routeEnhancers for this site with type pageType. When you do this, it is necessary that you also add the pageType for the Yoast Snippetpreview.<br />
                        Please add a mapping for type ' . self::FE_PREVIEW_TYPE . ' and map it for example to \'yoast-snippetpreview.json\'.<br />
                        <strong><a href="https://docs.typo3.org/typo3cms/extensions/core/Changelog/9.5/Feature-86160-PageTypeEnhancerForMappingTypeParameter.html" target="_blank">You can find an example configuration here.</a></strong>
                    </div>
                </div>';
            }
            return $returnHtml;
        }
        return '';
    }

    /**
     * Try to resolve a supported locale based on the user settings
     * take the configured locale dependencies into account
     * so if the TYPO3 interface is tailored for a specific dialect
     * the local of a parent language might be used
     *
     * @return string|null
     */
    protected function getInterfaceLocale()
    {
        $locale = null;
        $languageChain = null;

        if ($GLOBALS['BE_USER'] instanceof CMS\Core\Authentication\BackendUserAuthentication
            && is_array($GLOBALS['BE_USER']->uc)
            && array_key_exists('lang', $GLOBALS['BE_USER']->uc)
            && !empty($GLOBALS['BE_USER']->uc['lang'])
        ) {
            $languageChain = $this->localeService->getLocaleDependencies(
                $GLOBALS['BE_USER']->uc['lang']
            );

            array_unshift($languageChain, $GLOBALS['BE_USER']->uc['lang']);
        }

        // try to find a matching locale available for this plugins UI
        // take configured locale dependencies into account
        if ($languageChain !== null
            && ($suitableLocales = array_intersect(
                $languageChain,
                $this->configuration['translations']['availableLocales']
            )) !== false
            && count($suitableLocales) > 0
        ) {
            $locale = array_shift($suitableLocales);
        }

        // if a locale couldn't be resolved try if an entry of the
        // language dependency chain matches legacy mapping
        if ($locale === null && $languageChain !== null
            && ($suitableLanguageKeys = array_intersect(
                $languageChain,
                array_flip(
                    $this->configuration['translations']['languageKeyToLocaleMapping']
                )
            )) !== false
            && count($suitableLanguageKeys) > 0
        ) {
            $locale =
                $this->configuration['translations']['languageKeyToLocaleMapping'][array_shift($suitableLanguageKeys)];
        }

        return $locale;
    }

    protected function getTargetUrl(int $pageId, int $languageId): string
    {
        $permissionClause = $this->getBackendUser()->getPagePermsClause(CMS\Core\Type\Bitmask\Permission::PAGE_SHOW);
        $pageRecord = CMS\Backend\Utility\BackendUtility::readPageAccess($pageId, $permissionClause);
        if ($pageRecord) {
            $rootLine = CMS\Backend\Utility\BackendUtility::BEgetRootLine($pageId);
            // Mount point overlay: Set new target page id and mp parameter
            $pageRepository = GeneralUtility::makeInstance(CMS\Frontend\Page\PageRepository::class);
            $siteFinder = GeneralUtility::makeInstance(CMS\Core\Site\SiteFinder::class);
            $site = $siteFinder->getSiteByPageId($pageId, $rootLine);
            $finalPageIdToShow = $pageId;
            $mountPointInformation = $pageRepository->getMountPointInfo($pageId);
            if ($mountPointInformation && $mountPointInformation['overlay']) {
                // New page id
                $finalPageIdToShow = $mountPointInformation['mount_pid'];
                $additionalGetVars .= '&MP=' . $mountPointInformation['MPvar'];
            }
            if ($site instanceof CMS\Core\Site\Entity\Site) {
                $this->checkRouteEnhancers($site);

                $additionalQueryParams = [];
                parse_str($additionalGetVars, $additionalQueryParams);
                $additionalQueryParams['_language'] = $site->getLanguageById($languageId);
                $uriToCheck = (string)$site->getRouter()->generateUri($finalPageIdToShow, $additionalQueryParams);

                $additionalQueryParams['type'] = self::FE_PREVIEW_TYPE;
                $additionalQueryParams['uriToCheck'] = urlencode($uriToCheck);
                $uri = (string)$site->getRouter()->generateUri($site->getRootPageId(), $additionalQueryParams);
            } else {
                $uri = CMS\Backend\Utility\BackendUtility::getPreviewUrl($finalPageIdToShow, '', $rootLine, '', '', $additionalGetVars);
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
    protected function checkRouteEnhancers(CMS\Core\Site\Entity\Site $site)
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
     * @return CMS\Core\Authentication\BackendUserAuthentication
     */
    protected function getBackendUser(): CMS\Core\Authentication\BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }
}
