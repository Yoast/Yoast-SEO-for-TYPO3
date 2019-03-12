<?php
namespace YoastSeoForTypo3\YoastSeo\Form\Element;

use TYPO3\CMS\Backend\Form\AbstractNode;
use TYPO3\CMS\Backend\Form\NodeFactory;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Localization\Locales;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Type\Bitmask\Permission;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;
use TYPO3\CMS\Frontend\Page\CacheHashCalculator;
use TYPO3\CMS\Frontend\Page\PageRepository;
use YoastSeoForTypo3\YoastSeo\Utility\YoastUtility;

class SnippetPreview extends AbstractNode
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
     * @var StandaloneView
     */
    protected $templateView;

    /**
     * @var string
     */
    protected $titleField = 'title';

    /**
     * @var string
     */
    protected $descriptionField = 'description';

    /**
     * @var string
     */
    protected $table = 'pages';

    /**
     * @var string
     */
    protected $previewUrl = '';

    /**
     * @var int
     */
    protected $languageId = 0;

    /**
     * @var string
     */
    protected $viewScript = '/?id=';

    /**
     * @var Locales
     */
    protected $localeService;

    /**
     * @var array
     */
    protected $configuration = [
        'translations' => [
            'availableLocales' => [],
            'languageKeyToLocaleMapping' => []
        ],
    ];

    /**
     * @param NodeFactory $nodeFactory
     * @param array $data
     */
    public function __construct(NodeFactory $nodeFactory, array $data)
    {
        parent::__construct($nodeFactory, $data);

        if (array_key_exists('yoast_seo', $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'])
            && is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['yoast_seo'])
        ) {
            ArrayUtility::mergeRecursiveWithOverrule(
                $this->configuration,
                $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['yoast_seo']
            );
        }

        $this->localeService = GeneralUtility::makeInstance(Locales::class);

        $this->templateView = GeneralUtility::makeInstance(StandaloneView::class);
        $this->templateView->setTemplatePathAndFilename(
            GeneralUtility::getFileAbsFileName('EXT:yoast_seo/Resources/Private/Templates/TCA/SnippetPreview.html')
        );

        if (array_key_exists('titleField', (array)$this->data['parameterArray']['fieldConf']['config']['settings']) &&
            $this->data['parameterArray']['fieldConf']['config']['settings']['titleField']
        ) {
            $this->titleField = $this->data['parameterArray']['fieldConf']['config']['settings']['titleField'];
        }

        if (array_key_exists(
            'descriptionField',
            (array)$this->data['parameterArray']['fieldConf']['config']['settings']
        ) &&
            $this->data['parameterArray']['fieldConf']['config']['settings']['descriptionField']
        ) {
            $this->descriptionField =
                $this->data['parameterArray']['fieldConf']['config']['settings']['descriptionField'];
        }

        if (array_key_exists('0', (array)$this->data['databaseRow']['sys_language_uid']) &&
            $this->data['databaseRow']['sys_language_uid'][0]
        ) {
            $this->languageId = (int)$this->data['databaseRow']['sys_language_uid'][0];
        }

        $this->table = $this->data['tableName'];

        $this->previewUrl = $this->getPreviewUrl();
    }

    public function render()
    {
        $allowedDoktypes = YoastUtility::getAllowedDoktypes();
        $resultArray = $this->initializeResultArray();
        $publicResourcesPath = PathUtility::getAbsoluteWebPath('../typo3conf/ext/yoast_seo/Resources/Public/');
        $resultArray['stylesheetFiles'][] = $publicResourcesPath . 'CSS/yoast-seo-tca.min.css';

        if ($this->data['tableName'] != 'pages' || in_array((int)$this->data['databaseRow']['doktype'][0], $allowedDoktypes)) {
            $firstFocusKeyword = YoastUtility::getFocusKeywordOfPage((int)$this->data['databaseRow']['uid'], $this->data['tableName']);

            $this->templateView->assign('translations', $this->getTranslations());
            $this->templateView->assign('previewUrl', $this->previewUrl);
            $this->templateView->assign('previewTargetId', $this->data['fieldName']);
            $this->templateView->assign('titleFieldSelector', $this->getFieldSelector($this->titleField));
            $this->templateView->assign('descriptionFieldSelector', $this->getFieldSelector($this->descriptionField));
            $this->templateView->assign('scoreReadabilityFieldSelector', $this->getFieldSelector('tx_yoastseo_score_readability'));
            $this->templateView->assign('databaseRow', $this->data['databaseRow']);
            $this->templateView->assign('scoreSeoFieldSelector', $this->getFieldSelector('tx_yoastseo_score_seo'));
            $this->templateView->assign('focusKeyword', $firstFocusKeyword);
            $this->templateView->assign('vanillaUid', $this->data['vanillaUid']);
            $this->templateView->assign('tableName', $this->data['tableName']);
            $this->templateView->assign('languageId', $this->languageId);
        } else {
            $this->templateView->assign('wrongDoktype', true);
        }
        $resultArray['html'] = $this->templateView->render();

        return $resultArray;
    }

    /**
     * @param string $field
     * @return string
     */
    protected function getFieldSelector($field)
    {
        $element = 'data' . str_replace('tx_yoastseo_snippetpreview', $field, $this->data['elementBaseName']);

        return $element;
    }

    /**
     * @return string
     */
    protected function getTranslations()
    {
        $interfaceLocale = $this->getInterfaceLocale();

        if ($interfaceLocale !== null
            && ($translationFilePath = sprintf(
                static::APP_TRANSLATION_FILE_PATTERN,
                $interfaceLocale
            )) !== false
            && ($translationFilePath = GeneralUtility::getFileAbsFileName(
                $translationFilePath
            )) !== false
            && file_exists($translationFilePath)
        ) {
            return (string)file_get_contents($translationFilePath);
        }
    }

    /**
     * @return string
     */
    protected function getPreviewUrl()
    {
        $currentPageId = $this->data['effectivePid'];
        $recordId = $this->data['vanillaUid'];

        $recordArray = BackendUtility::getRecord($this->table, $recordId);

        $pageTsConfig = BackendUtility::getPagesTSconfig($currentPageId);
        $previewConfiguration = isset($pageTsConfig['TCEMAIN.']['preview.'][$this->table . '.'])
            ? $pageTsConfig['TCEMAIN.']['preview.'][$this->table . '.']
            : [];

        // find the right preview page id
        $previewPageId = 0;
        if (isset($previewConfiguration['previewPageId'])) {
            $previewPageId = $previewConfiguration['previewPageId'];
        }

        // if no preview page was configured
        if (!$previewPageId) {
            $rootPageData = null;
            $rootLine = BackendUtility::BEgetRootLine((int)$currentPageId);
            $currentPage = reset($rootLine);
            // Allow all doktypes below 200
            // This makes custom doktype work as well with opening a frontend page.
            if ((int)$currentPage['doktype'] <= PageRepository::DOKTYPE_SPACER) {
                // try the current page
                $previewPageId = $currentPageId;
            } else {
                // or search for the root page
                foreach ($rootLine as $page) {
                    if ($page['is_siteroot']) {
                        $rootPageData = $page;
                        break;
                    }
                }
                $previewPageId = isset($rootPageData)
                    ? (int)$rootPageData['uid']
                    : (int)$currentPageId;
            }
        }

        $linkParameters = [];
        $languageId = 0;
        // language handling
        $languageField = $GLOBALS['TCA'][$this->table]['ctrl']['languageField'] ?? '';

        if ($languageField && !empty($recordArray[$languageField])) {
            $l18nPointer = $GLOBALS['TCA'][$this->table]['ctrl']['transOrigPointerField'] ?? '';
            if ($l18nPointer && !empty($recordArray[$l18nPointer])) {
                if (isset($previewConfiguration['useDefaultLanguageRecord'])
                    && !$previewConfiguration['useDefaultLanguageRecord']) {
                    // use parent record
                    $recordId = $recordArray[$l18nPointer];
                }

                $previewPageId = $recordArray[$l18nPointer];
            }
            $languageId = $recordArray[$languageField];
        }

        // map record data to GET parameters
        if (isset($previewConfiguration['fieldToParameterMap.'])) {
            foreach ($previewConfiguration['fieldToParameterMap.'] as $field => $parameterName) {
                $value = $recordArray[$field];
                if ($field === 'uid') {
                    $value = $recordId;
                }
                $linkParameters[$parameterName] = $value;
            }
        }

        // add/override parameters by configuration
        if (isset($previewConfiguration['additionalGetParameters.'])) {
            $additionalGetParameters = [];
            $this->parseAdditionalGetParameters(
                $additionalGetParameters,
                $previewConfiguration['additionalGetParameters.']
            );
            $linkParameters = array_replace($linkParameters, $additionalGetParameters);
        }

        if (!empty($previewConfiguration['useCacheHash'])) {
            /** @var CacheHashCalculator */
            $cacheHashCalculator = GeneralUtility::makeInstance(CacheHashCalculator::class);
            $fullLinkParameters = GeneralUtility::implodeArrayForUrl(
                '',
                array_merge($linkParameters, ['id' => $previewPageId])
            );
            $cacheHashParameters = $cacheHashCalculator->getRelevantParameters($fullLinkParameters);
            $linkParameters['cHash'] = $cacheHashCalculator->calculateCacheHash($cacheHashParameters);
        }

        $additionalParamsForUrl = GeneralUtility::implodeArrayForUrl('', $linkParameters, '', false, true);

        return $this->getTargetUrl($previewPageId, $languageId, $additionalParamsForUrl);
    }

    /**
     * Migrates a set of (possibly nested) GET parameters in TypoScript syntax to a plain array
     *
     * This basically removes the trailing dots of sub-array keys in TypoScript.
     * The result can be used to create a query string with GeneralUtility::implodeArrayForUrl().
     *
     * @param array $parameters Should be an empty array by default
     * @param array $typoScript The TypoScript configuration
     */
    protected function parseAdditionalGetParameters(array &$parameters, array $typoScript)
    {
        foreach ($typoScript as $key => $value) {
            if (is_array($value)) {
                $key = rtrim($key, '.');
                $parameters[$key] = [];
                $this->parseAdditionalGetParameters($parameters[$key], $value);
            } else {
                $parameters[$key] = $value;
            }
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

        if ($GLOBALS['BE_USER'] instanceof BackendUserAuthentication
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

    protected function getTargetUrl(int $pageId, int $languageId, string $additionalGetVars = ''): string
    {
        $permissionClause = $this->getBackendUser()->getPagePermsClause(Permission::PAGE_SHOW);
        $pageRecord = BackendUtility::readPageAccess($pageId, $permissionClause);
        if ($pageRecord) {
            $rootLine = BackendUtility::BEgetRootLine($pageId);
            // Mount point overlay: Set new target page id and mp parameter
            $pageRepository = GeneralUtility::makeInstance(PageRepository::class);
            $siteFinder = GeneralUtility::makeInstance(SiteFinder::class);
            $site = $siteFinder->getSiteByPageId($pageId, $rootLine);
            $finalPageIdToShow = $pageId;
            $mountPointInformation = $pageRepository->getMountPointInfo($pageId);
            if ($mountPointInformation && $mountPointInformation['overlay']) {
                // New page id
                $finalPageIdToShow = $mountPointInformation['mount_pid'];
                $additionalGetVars .= '&MP=' . $mountPointInformation['MPvar'];
            }
            if ($site instanceof Site) {
                $additionalQueryParams = [];
                parse_str($additionalGetVars, $additionalQueryParams);
                $additionalQueryParams['_language'] = $site->getLanguageById($languageId);
                $uriToCheck = (string)$site->getRouter()->generateUri($finalPageIdToShow, $additionalQueryParams);

                $additionalQueryParams['type'] = self::FE_PREVIEW_TYPE;
                $additionalQueryParams['uriToCheck'] = urlencode($uriToCheck);
                $additionalQueryParams['_language'] = $site->getLanguageById($languageId);
                $uri = (string)$site->getRouter()->generateUri($site->getRootPageId(), $additionalQueryParams);
            } else {
                $uri = BackendUtility::getPreviewUrl($finalPageIdToShow, '', $rootLine, '', '', $additionalGetVars);
            }
            return $uri;
        }
        return '#';
    }

    /**
     * @return BackendUserAuthentication
     */
    protected function getBackendUser(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }
}
