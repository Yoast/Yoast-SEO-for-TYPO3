<?php
declare(strict_types=1);
namespace YoastSeoForTypo3\YoastSeo\Form\Element;

use TYPO3\CMS\Backend\Form\AbstractNode;
use TYPO3\CMS\Backend\Form\NodeFactory;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;
use TYPO3\CMS\Frontend\Page\CacheHashCalculator;
use YoastSeoForTypo3\YoastSeo\Service\LocaleService;
use YoastSeoForTypo3\YoastSeo\Service\UrlService;
use YoastSeoForTypo3\YoastSeo\Utility\JsonConfigUtility;
use YoastSeoForTypo3\YoastSeo\Utility\PathUtility;
use YoastSeoForTypo3\YoastSeo\Utility\YoastUtility;

class SnippetPreview extends AbstractNode
{
    /**
     * @var StandaloneView
     */
    protected StandaloneView $templateView;

    /**
     * @var string
     */
    protected string $titleField = 'title';

    /**
     * @var string
     */
    protected string $pageTitleField = 'title';

    /**
     * @var string
     */
    protected string $descriptionField = 'description';

    /**
     * @var string
     */
    protected string $focusKeywordField = 'tx_yoastseo_focuskeyword';

    /**
     * @var string
     */
    protected string $focusKeywordSynonymsField = 'tx_yoastseo_focuskeyword_synonyms';

    /**
     * @var string
     */
    protected string $cornerstoneField = 'tx_yoastseo_cornerstone';

    /**
     * @var string
     */
    protected string $relatedKeyphrases = 'tx_yoastseo_focuskeyword_premium';

    /**
     * @var string
     */
    protected string $table = 'pages';

    /**
     * @var string
     */
    protected string $previewUrl = '';

    /**
     * @var int
     */
    protected int $languageId = 0;

    /**
     * @var \YoastSeoForTypo3\YoastSeo\Service\LocaleService
     */
    protected LocaleService $localeService;

    /**
     * @var \YoastSeoForTypo3\YoastSeo\Service\UrlService
     */
    protected UrlService $urlService;

    /**
     * @var array
     */
    protected array $configuration = [
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

        if (isset($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['yoast_seo'])
            && is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['yoast_seo'])
        ) {
            ArrayUtility::mergeRecursiveWithOverrule(
                $this->configuration,
                $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['yoast_seo']
            );
        }

        $this->localeService = GeneralUtility::makeInstance(LocaleService::class, $this->configuration);
        $this->urlService = GeneralUtility::makeInstance(UrlService::class);

        $this->templateView = GeneralUtility::makeInstance(StandaloneView::class);
        $this->templateView->setPartialRootPaths(
            [GeneralUtility::getFileAbsFileName('EXT:yoast_seo/Resources/Private/Partials/TCA')]
        );
        $this->templateView->setTemplatePathAndFilename(
            GeneralUtility::getFileAbsFileName('EXT:yoast_seo/Resources/Private/Templates/TCA/SnippetPreview.html')
        );

        if (array_key_exists('titleField', (array)$this->data['parameterArray']['fieldConf']['config']['settings']) &&
            $this->data['parameterArray']['fieldConf']['config']['settings']['titleField']
        ) {
            $this->titleField = $this->data['parameterArray']['fieldConf']['config']['settings']['titleField'];
        }

        if (array_key_exists('pageTitleField', (array)$this->data['parameterArray']['fieldConf']['config']['settings']) &&
            $this->data['parameterArray']['fieldConf']['config']['settings']['pageTitleField']
        ) {
            $this->pageTitleField = $this->data['parameterArray']['fieldConf']['config']['settings']['pageTitleField'];
        }

        if (array_key_exists('descriptionField', (array)$this->data['parameterArray']['fieldConf']['config']['settings'])
            && $this->data['parameterArray']['fieldConf']['config']['settings']['descriptionField']
        ) {
            $this->descriptionField =
                $this->data['parameterArray']['fieldConf']['config']['settings']['descriptionField'];
        }

        if (isset($this->data['databaseRow']['sys_language_uid'])) {
            if (is_array($this->data['databaseRow']['sys_language_uid']) && count($this->data['databaseRow']['sys_language_uid']) > 0) {
                $this->languageId = (int)current($this->data['databaseRow']['sys_language_uid']);
            } else {
                $this->languageId = (int)$this->data['databaseRow']['sys_language_uid'];
            }
        }

        $this->table = $this->data['tableName'];

        $this->previewUrl = $this->getPreviewUrl();
    }

    public function render(): array
    {
        $jsonConfigUtility = GeneralUtility::makeInstance(JsonConfigUtility::class);
        $allowedDoktypes = YoastUtility::getAllowedDoktypes();
        $resultArray = $this->initializeResultArray();
        $resultArray['stylesheetFiles'][] = 'EXT:yoast_seo/Resources/Public/CSS/yoast.min.css';

        $premiumText = $this->getPremiumText();

        if ($this->data['tableName'] !== 'pages'
            || in_array((int)$this->data['databaseRow']['doktype'][0], $allowedDoktypes)) {
            $firstFocusKeyword = YoastUtility::getFocusKeywordOfPage(
                (int)$this->data['databaseRow']['uid'],
                $this->data['tableName']
            );

            $publicResourcesPath = PathUtility::getPublicPathToResources();
            $workerUrl = $publicResourcesPath . '/JavaScript/dist/worker.js';

            $config = [
                'urls' => [
                    'workerUrl' => $workerUrl,
                    'previewUrl' => $this->previewUrl,
                    'saveScores' => $this->urlService->getSaveScoresUrl(),
                    'prominentWords' => $this->urlService->getProminentWordsUrl(),
                ],
                'TCA' => 1,
                'useKeywordDistribution' => YoastUtility::isPremiumInstalled(),
                'useRelevantWords' => YoastUtility::isPremiumInstalled(),
                'isCornerstoneContent' => (bool)($this->data['databaseRow']['tx_yoastseo_cornerstone'] ?? false),
                'focusKeyphrase' => [
                    'keyword' => (string)$this->data['databaseRow']['tx_yoastseo_focuskeyword'],
                    'synonyms' => (string)($this->data['databaseRow']['tx_yoastseo_focuskeyword_synonyms'] ?? ''),
                ],
                'labels' => $this->localeService->getLabels(),
                'data' => [
                    'table' => $this->data['tableName'],
                    'uid' => (int)$this->data['databaseRow']['uid'],
                    'pid' => (int)$this->data['databaseRow']['pid'],
                    'languageId' => (int)$this->languageId
                ],
                'fieldSelectors' => [
                    'title' => $this->getFieldSelector($this->titleField),
                    'pageTitle' => $this->getFieldSelector($this->pageTitleField),
                    'description' => $this->getFieldSelector($this->descriptionField),
                    'focusKeyword' => $this->getFieldSelector($this->focusKeywordField),
                    'cornerstone' => $this->getFieldSelector($this->cornerstoneField),
                    'premiumKeyword' => YoastUtility::isPremiumInstalled() ? $this->getFieldSelector($this->relatedKeyphrases, true) : '',
                ],
                'translations' => $this->localeService->getTranslations(),
                'relatedKeyphrases' => YoastUtility::isPremiumInstalled() ? YoastUtility::getRelatedKeyphrases($this->data['tableName'], (int)$this->data['databaseRow']['uid']) : []
            ];

            if (YoastUtility::isPremiumInstalled()) {
                $config['fieldSelectors']['focusKeywordSynonyms'] = $this->getFieldSelector($this->focusKeywordSynonymsField);
            }

            $jsonConfigUtility->addConfig($config);

            $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);

            if (YoastUtility::inProductionMode() === true) {
                $pageRenderer->loadRequireJsModule('TYPO3/CMS/YoastSeo/dist/plugin');
            } else {
                $pageRenderer->addHeaderData('<script type="text/javascript" src="https://localhost:3333/typo3conf/ext/yoast_seo/Resources/Public/JavaScript/dist/plugin.js" async></script>');
            }

            $pageRenderer->loadRequireJsModule('TYPO3/CMS/YoastSeo/yoastModal');

            $this->templateView->assignMultiple([
                'previewUrl' => $this->previewUrl,
                'previewTargetId' => $this->data['fieldName'],
                'titleFieldSelector' => $this->getFieldSelector($this->titleField),
                'descriptionFieldSelector' => $this->getFieldSelector($this->descriptionField),
                'databaseRow' => $this->data['databaseRow'],
                'focusKeyword' => $firstFocusKeyword,
                'vanillaUid' => $this->data['vanillaUid'],
                'tableName' => $this->data['tableName'],
                'languageId' => $this->languageId,
                'previewContent' => $premiumText
            ]);
        } else {
            $this->templateView->assign('wrongDoktype', true);
        }
        $resultArray['html'] = $this->templateView->render();

        return $resultArray;
    }

    /**
     * Get premium text
     *
     * @return string
     */
    protected function getPremiumText(): string
    {
        if (!YoastUtility::isPremiumInstalled()) {
            return '
                <div class="yoast-snippet-preview-premium">
                    <a target="_blank" rel="noopener noreferrer" href="' . YoastUtility::getYoastLink('Go premium', 'page-properties-snippetpreview') . '">
                        <i class="fa fa-star"></i>' . $GLOBALS['LANG']->sL('LLL:EXT:yoast_seo/Resources/Private/Language/BackendModule.xlf:goPremium') . '
                    </a>
                </div>';
        }
        return '';
    }

    /**
     * @param string $field
     * @param bool $id
     * @return string
     */
    protected function getFieldSelector($field, $id = false): string
    {
        if ($id === true) {
            $element = 'data-' . $this->data['vanillaUid'] . '-' . $this->data['tableName'] . '-' . $this->data['vanillaUid'] . '-' . $field;
        } else {
            $element = 'data' . str_replace('tx_yoastseo_snippetpreview', $field, $this->data['elementBaseName']);
        }

        return $element;
    }

    /**
     * @return string
     */
    protected function getPreviewUrl(): string
    {
        $currentPageId = $this->data['effectivePid'];
        $recordId = $this->data['vanillaUid'];

        $recordArray = BackendUtility::getRecord($this->table, $recordId);

        $pageTsConfig = BackendUtility::getPagesTSconfig($currentPageId);
        $previewConfiguration = $pageTsConfig['TCEMAIN.']['preview.'][$this->table . '.'] ?? [];

        $previewPageId = $this->getPreviewPageId($currentPageId, $previewConfiguration);

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

                if ($this->table === 'pages') {
                    $previewPageId = $recordArray[$l18nPointer];
                }
            }
            $languageId = $recordArray[$languageField] > -1 ? $recordArray[$languageField] : 0;
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

        return $this->urlService->getPreviewUrl($previewPageId, $languageId, $additionalParamsForUrl);
    }

    /**
     * @param int $currentPageId
     * @param array $previewConfiguration
     * @return int
     */
    protected function getPreviewPageId(int $currentPageId, array $previewConfiguration): int
    {
        // find the right preview page id
        $previewPageId = (int)($previewConfiguration['previewPageId'] ?? 0);

        // if no preview page was configured
        if (!$previewPageId) {
            $rootPageData = null;
            $rootLine = BackendUtility::BEgetRootLine($currentPageId);
            $currentPage = reset($rootLine);
            // Allow all doktypes below 200
            // This makes custom doktype work as well with opening a frontend page.
            if ((int)$currentPage['doktype'] <= $this->getSpacerDoktype()) {
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
                    : $currentPageId;
            }
        }
        return $previewPageId;
    }

    /**
     * Migrates a set of (possibly nested) GET parameters in TypoScript syntax to a
     * plain array
     *
     * This basically removes the trailing dots of sub-array keys in TypoScript.
     * The result can be used to create a query string with
     * GeneralUtility::implodeArrayForUrl().
     *
     * @param array $parameters Should be an empty array by default
     * @param array $typoScript The TypoScript configuration
     */
    protected function parseAdditionalGetParameters(
        array &$parameters,
        array $typoScript
    ): void {
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
     * @return int
     */
    protected function getSpacerDoktype(): int
    {
        if (class_exists(PageRepository::class)) {
            return PageRepository::DOKTYPE_SPACER;
        }
        return \TYPO3\CMS\Frontend\Page\PageRepository::DOKTYPE_SPACER;
    }

    /**
     * @return BackendUserAuthentication
     */
    protected function getBackendUser(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }
}
