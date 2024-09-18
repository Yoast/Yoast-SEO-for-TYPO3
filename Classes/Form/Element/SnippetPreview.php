<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Form\Element;

use TYPO3\CMS\Backend\Form\AbstractNode;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;
use TYPO3\CMS\Frontend\Page\CacheHashCalculator;
use YoastSeoForTypo3\YoastSeo\Service\SnippetPreviewService;
use YoastSeoForTypo3\YoastSeo\Service\UrlService;
use YoastSeoForTypo3\YoastSeo\Utility\YoastUtility;

class SnippetPreview extends AbstractNode
{
    protected string $titleField = 'title';
    protected string $pageTitleField = 'title';
    protected string $descriptionField = 'description';
    protected string $focusKeywordField = 'tx_yoastseo_focuskeyword';
    protected string $focusKeywordSynonymsField = 'tx_yoastseo_focuskeyword_synonyms';
    protected string $cornerstoneField = 'tx_yoastseo_cornerstone';
    protected string $relatedKeyphrases = 'tx_yoastseo_focuskeyword_related';
    protected string $table = 'pages';
    protected string $previewUrl = '';
    protected int $languageId = 0;

    protected UrlService $urlService;

    public function render(): array
    {
        $this->initialize();

        $resultArray = $this->initializeResultArray();
        $resultArray['stylesheetFiles'][] = 'EXT:yoast_seo/Resources/Public/CSS/yoast.min.css';

        $templateView = $this->getTemplateView();

        if ($this->data['tableName'] === 'pages'
            && !in_array((int)($this->data['databaseRow']['doktype'][0] ?? 0), YoastUtility::getAllowedDoktypes(), true)) {
            $templateView->assign('wrongDoktype', true);
            $resultArray['html'] = $templateView->render();
            return $resultArray;
        }

        $firstFocusKeyword = YoastUtility::getFocusKeywordOfRecord(
            (int)$this->data['databaseRow']['uid'],
            $this->data['tableName']
        );

        $snippetPreviewConfiguration = [
            'TCA' => 1,
            'data' => [
                'table' => $this->data['tableName'],
                'uid' => (int)($this->data['defaultLanguagePageRow']['uid'] ?? $this->data['databaseRow']['uid']),
                'pid' => (int)$this->data['databaseRow']['pid'],
                'languageId' => $this->languageId
            ],
            'fieldSelectors' => [
                'title' => $this->getFieldSelector($this->titleField),
                'pageTitle' => $this->getFieldSelector($this->pageTitleField),
                'description' => $this->getFieldSelector($this->descriptionField),
                'focusKeyword' => $this->getFieldSelector($this->focusKeywordField),
                'focusKeywordSynonyms' => $this->getFieldSelector($this->focusKeywordSynonymsField),
                'cornerstone' => $this->getFieldSelector($this->cornerstoneField),
                'relatedKeyword' => $this->getFieldSelector($this->relatedKeyphrases, true),
            ],
            'relatedKeyphrases' => YoastUtility::getRelatedKeyphrases(
                $this->data['tableName'],
                (int)$this->data['databaseRow']['uid']
            )
        ];

        $snippetPreviewService = GeneralUtility::makeInstance(SnippetPreviewService::class);
        $snippetPreviewService->buildSnippetPreview(
            $this->previewUrl,
            $this->data['databaseRow'],
            $snippetPreviewConfiguration
        );

        $templateView->assignMultiple([
            'previewUrl' => $this->previewUrl,
            'previewTargetId' => $this->data['fieldName'],
            'titleFieldSelector' => $this->getFieldSelector($this->titleField),
            'descriptionFieldSelector' => $this->getFieldSelector($this->descriptionField),
            'databaseRow' => $this->data['databaseRow'],
            'focusKeyword' => $firstFocusKeyword,
            'vanillaUid' => $this->data['vanillaUid'],
            'tableName' => $this->data['tableName'],
            'languageId' => $this->languageId,
        ]);
        $resultArray['html'] = $templateView->render();

        return $resultArray;
    }

    protected function initialize(): void
    {
        $this->urlService = GeneralUtility::makeInstance(UrlService::class);

        if (!empty($this->data['parameterArray']['fieldConf']['config']['settings']['titleField'] ?? '')) {
            $this->titleField = $this->data['parameterArray']['fieldConf']['config']['settings']['titleField'];
        }

        if (!empty($this->data['parameterArray']['fieldConf']['config']['settings']['pageTitleField'] ?? '')) {
            $this->pageTitleField = $this->data['parameterArray']['fieldConf']['config']['settings']['pageTitleField'];
        }

        if (!empty($this->data['parameterArray']['fieldConf']['config']['settings']['descriptionField'] ?? '')) {
            $this->descriptionField =
                $this->data['parameterArray']['fieldConf']['config']['settings']['descriptionField'];
        }

        if (isset($this->data['databaseRow']['sys_language_uid'])) {
            if (is_array($this->data['databaseRow']['sys_language_uid']) && count(
                    $this->data['databaseRow']['sys_language_uid']
                ) > 0) {
                $this->languageId = (int)current($this->data['databaseRow']['sys_language_uid']);
            } else {
                $this->languageId = (int)$this->data['databaseRow']['sys_language_uid'];
            }
        }

        $this->table = $this->data['tableName'];

        $this->previewUrl = $this->getPreviewUrl();
    }

    protected function getTemplateView(): StandaloneView
    {
        $templateView = GeneralUtility::makeInstance(StandaloneView::class);
        $templateView->setPartialRootPaths(
            [GeneralUtility::getFileAbsFileName('EXT:yoast_seo/Resources/Private/Partials/TCA')]
        );
        $templateView->setTemplatePathAndFilename(
            GeneralUtility::getFileAbsFileName('EXT:yoast_seo/Resources/Private/Templates/TCA/SnippetPreview.html')
        );
        return $templateView;
    }

    protected function getFieldSelector(string $field, bool $id = false): string
    {
        if ($id === true) {
            $element = 'data-' . $this->data['vanillaUid'] . '-' . $this->data['tableName'] . '-' . $this->data['vanillaUid'] . '-' . $field;
        } else {
            $element = 'data' . str_replace('tx_yoastseo_snippetpreview', $field, $this->data['elementBaseName']);
        }

        return $element;
    }

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
}
