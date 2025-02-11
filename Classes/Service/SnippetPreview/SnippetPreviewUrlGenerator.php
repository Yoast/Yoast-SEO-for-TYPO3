<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Service\SnippetPreview;

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Page\CacheHashCalculator;
use YoastSeoForTypo3\YoastSeo\Service\UrlService;

class SnippetPreviewUrlGenerator
{
    public function __construct(
        protected CacheHashCalculator $cacheHashCalculator,
        protected UrlService $urlService
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public function getPreviewUrl(array $data): string
    {
        $currentPageId = (int)$data['effectivePid'];
        $recordId = (int)$data['vanillaUid'];
        $tableName = $data['tableName'];

        $recordArray = $this->getRecordArray($tableName, $recordId);
        $previewConfiguration = $this->getPreviewConfiguration($currentPageId, $tableName);

        $previewPageId = $this->getPreviewPageId($currentPageId, $previewConfiguration);

        $linkParameters = [];
        $languageId = 0;
        // language handling
        $languageField = $GLOBALS['TCA'][$tableName]['ctrl']['languageField'] ?? '';

        if ($languageField && !empty($recordArray[$languageField])) {
            $l18nPointer = $GLOBALS['TCA'][$tableName]['ctrl']['transOrigPointerField'] ?? '';
            if ($l18nPointer && !empty($recordArray[$l18nPointer])) {
                if (isset($previewConfiguration['useDefaultLanguageRecord'])
                    && !$previewConfiguration['useDefaultLanguageRecord']) {
                    // use parent record
                    $recordId = $recordArray[$l18nPointer];
                }

                if ($tableName === 'pages') {
                    $previewPageId = $recordArray[$l18nPointer];
                }
            }
            $languageId = $recordArray[$languageField] > -1 ? $recordArray[$languageField] : 0;
        }

        // map record data to GET parameters
        if (isset($previewConfiguration['fieldToParameterMap.'])) {
            foreach ($previewConfiguration['fieldToParameterMap.'] as $field => $parameterName) {
                $value = $recordArray[$field] ?? '';
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
            $fullLinkParameters = GeneralUtility::implodeArrayForUrl(
                '',
                array_merge($linkParameters, ['id' => $previewPageId])
            );
            $cacheHashParameters = $this->cacheHashCalculator->getRelevantParameters($fullLinkParameters);
            $linkParameters['cHash'] = $this->cacheHashCalculator->calculateCacheHash($cacheHashParameters);
        }

        $additionalParamsForUrl = GeneralUtility::implodeArrayForUrl('', $linkParameters, '', false, true);

        return $this->urlService->getPreviewUrl($previewPageId, $languageId, $additionalParamsForUrl);
    }

    /**
     * @param array<string, mixed> $previewConfiguration
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
     * @return array<string, mixed>
     */
    protected function getRecordArray(string $tableName, int $recordId): array
    {
        return BackendUtility::getRecord($tableName, $recordId) ?? [];
    }

    /**
     * @return array<string, mixed>
     */
    protected function getPreviewConfiguration(int $currentPageId, string $tableName): array
    {
        $pageTsConfig = BackendUtility::getPagesTSconfig($currentPageId);
        return $pageTsConfig['TCEMAIN.']['preview.'][$tableName . '.'] ?? [];
    }

    /**
     * Migrates a set of (possibly nested) GET parameters in TypoScript syntax to a
     * plain array
     *
     * This basically removes the trailing dots of sub-array keys in TypoScript.
     * The result can be used to create a query string with
     * GeneralUtility::implodeArrayForUrl().
     *
     * @param array<string, mixed> $parameters Should be an empty array by default
     * @param array<string, mixed> $typoScript The TypoScript configuration
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
