<?php
namespace YoastSeoForTypo3\YoastSeo\Backend;

use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS;

class PageLayoutHeader
{

    /**
     * @var string
     */
    const COLUMN_NAME = 'tx_yoastseo_focuskeyword';

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
     * Initialize the page renderer
     */
    public function __construct()
    {
        if (array_key_exists('yoast_seo', $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'])
            && is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['yoast_seo'])
        ) {
            ArrayUtility::mergeRecursiveWithOverrule($this->configuration, $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['yoast_seo']);
        }

        $this->localeService = CMS\Core\Utility\GeneralUtility::makeInstance(CMS\Core\Localization\Locales::class);
        $this->pageRenderer = CMS\Core\Utility\GeneralUtility::makeInstance(CMS\Core\Page\PageRenderer::class);
    }

    /**
     * @return string
     */
    public function render()
    {
        /** @var CMS\Backend\Controller\PageLayoutController $pageLayoutController */
        $pageLayoutController = $GLOBALS['SOBE'];

        $currentPage = null;
        $focusKeyword = '';
        $previewDataUrl = '';
        $recordId = 0;
        $tableName = 'pages';
        $targetElementId = uniqid('_YoastSEO_panel_');
        $publicResourcesPath = CMS\Core\Utility\ExtensionManagementUtility::extRelPath('yoast_seo')
            . 'Resources/Public/';

        if ($pageLayoutController instanceof CMS\Backend\Controller\PageLayoutController
            && (int) $pageLayoutController->id > 0
            && (int) $pageLayoutController->current_sys_language === 0
        ) {
            $currentPage = CMS\Backend\Utility\BackendUtility::getRecord(
                'pages',
                (int) $pageLayoutController->id
            );
        } elseif ($pageLayoutController instanceof CMS\Backend\Controller\PageLayoutController
            && (int) $pageLayoutController->id > 0
            && (int) $pageLayoutController->current_sys_language > 0
        ) {
            $overlayRecords = CMS\Backend\Utility\BackendUtility::getRecordLocalization(
                'pages',
                (int) $pageLayoutController->id,
                (int) $pageLayoutController->current_sys_language
            );

            if (is_array($overlayRecords) && array_key_exists(0, $overlayRecords) && is_array($overlayRecords[0])) {
                $currentPage = $overlayRecords[0];

                $tableName = 'pages_language_overlay';
            }
        }

        if (is_array($currentPage) && array_key_exists(self::COLUMN_NAME, $currentPage)) {
            $focusKeyword = $currentPage[self::COLUMN_NAME];

            $recordId = $currentPage['uid'];

            $domain = CMS\Backend\Utility\BackendUtility::getViewDomain($currentPage['uid']);

            // Allow Overwrite of the domain via ExtConf
            if (array_key_exists('previewDomain', $this->configuration) && $this->configuration['previewDomain']) {
                try {
                    $protocol = GeneralUtility::getIndpEnv('TYPO3_SSL') ? 'https' : 'http';
                } catch (\UnexpectedValueException $e) {
                    $protocol = 'http';
                }

                if (strpos($this->configuration['previewDomain'], '://') !== false) {
                    list($protocol, $domainName) = explode('://', $this->configuration['previewDomain']);
                } else {
                    $domainName = $this->configuration['previewDomain'];
                }
                $domain = $protocol . '://' . $domainName;
            }

            $previewDataUrl = vsprintf(
                $domain . $this->configuration['previewUrlTemplate'],
                array(
                    $pageLayoutController->id,
                    self::FE_PREVIEW_TYPE,
                    $pageLayoutController->current_sys_language
                )
            );
        }

        $allowedDoktypes = $this->getAllowedDoktypes();
        if (is_array($currentPage) &&
            array_key_exists('doktype', $currentPage) &&
            in_array((int)$currentPage['doktype'], $allowedDoktypes, true)
        ) {
            $interfaceLocale = $this->getInterfaceLocale();

            if ($interfaceLocale !== null
                && ($translationFilePath = sprintf(
                    self::APP_TRANSLATION_FILE_PATTERN,
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

            $this->pageRenderer->addJsInlineCode(
                'YoastSEO settings',
                'var tx_yoast_seo = tx_yoast_seo || {};'
                . ' tx_yoast_seo.settings = '
                . json_encode(
                    array(
                        'focusKeyword' => $focusKeyword,
                        'preview' => $previewDataUrl,
                        'recordId' => $recordId,
                        'recordTable' => $tableName,
                        'targetElementId' => $targetElementId,
                        'editable' => 0,
                        'disableSlug' => 1
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

            $this->pageRenderer->loadRequireJsModule('YoastSEO/app');

            $this->pageRenderer->addCssFile(
                $publicResourcesPath . 'CSS/yoast-seo.min.css'
            );

            $returnUrl = CMS\Backend\Utility\BackendUtility::getModuleUrl('web_layout', array('id' => (int) $pageLayoutController->id));

            $parameters = array(
                'tx_yoastseo_web_yoastseoseoplugin[id]' => (int) $pageLayoutController->id,
                'tx_yoastseo_web_yoastseoseoplugin[language]' => (int) $pageLayoutController->current_sys_language,
                'tx_yoastseo_web_yoastseoseoplugin[returnUrl]' => rawurlencode($returnUrl)
            );
            $seoUrl = CMS\Backend\Utility\BackendUtility::getModuleUrl('web_YoastSeoSeoPlugin', $parameters);

            return '<div class="t3-page-column-header">
                    <div class="t3-page-column-header-icons btn-group btn-group-sm">
                        <a href="' . $seoUrl . '" title="" class="btn btn-default">
                            <span class="t3js-icon icon icon-size-small icon-state-default icon-actions-document-open" data-identifier="actions-document-open">
                                <span class="icon-markup">
                                    <img src="/typo3/sysext/core/Resources/Public/Icons/T3Icons/actions/actions-document-open.svg" width="16" height="16">
                                </span>
                            </span>
                        </a>
                    </div>
                    <div class="t3-page-column-header-label">Yoast SEO</div>
                </div>
                <input id="focusKeyword" style="display: none" />
                <div id="' . $targetElementId . '" class="t3-grid-cell yoastSeo yoastSeo--small">
                	<!-- ' . $targetElementId . ' -->
                	<div class="spinner">
					  <div class="bounce1"></div>
					  <div class="bounce2"></div>
					  <div class="bounce3"></div>
					</div>
                </div>';
        } else {
            return '';
        }
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
            $locale = $this->configuration['translations']['languageKeyToLocaleMapping'][array_shift($suitableLanguageKeys)];
        }

        return $locale;
    }

    /**
     * @return array
     */
    protected function getAllowedDoktypes()
    {
        $allowedDoktypes = [1];     // By default only add normal pages

        /** @var CMS\Extbase\Object\ObjectManager $objectManager */
        $objectManager = CMS\Core\Utility\GeneralUtility::makeInstance(CMS\Extbase\Object\ObjectManager::class);
        /** @var CMS\Extbase\Configuration\ConfigurationManager $configurationManager */
        $configurationManager = $objectManager->get(CMS\Extbase\Configuration\ConfigurationManager::class);
        $configuration = $configurationManager->getConfiguration(CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS, 'yoastseo');

        if (is_array($configuration) && array_key_exists('allowedDoktypes', $configuration)
        ) {
            foreach ($configuration['allowedDoktypes'] as $key => $doktype) {
                if (!in_array($doktype, $allowedDoktypes)) {
                    $allowedDoktypes[] = (int)$doktype;
                }
            }
        }

        return $allowedDoktypes;
    }
}
