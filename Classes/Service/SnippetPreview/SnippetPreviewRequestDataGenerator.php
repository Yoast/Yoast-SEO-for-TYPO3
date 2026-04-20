<?php

/**
 * This file is part of the "yoast_seo" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Service\SnippetPreview;

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Page\CacheHashCalculator;
use YoastSeoForTypo3\YoastSeo\Constants\TableNames;
use YoastSeoForTypo3\YoastSeo\Dto\RequestData;
use YoastSeoForTypo3\YoastSeo\Service\UrlService;

class SnippetPreviewRequestDataGenerator
{
    public function __construct(
        protected CacheHashCalculator $cacheHashCalculator,
        protected UrlService $urlService
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public function getRequestData(array $data): RequestData
    {
        $currentPageId = (int)$data['effectivePid'];
        $recordId = (int)$data['vanillaUid'];
        $tableName = $data['tableName'];

        $record = $this->getRecordArray($tableName, $recordId);
        $previewConfiguration = $this->getPreviewConfiguration($currentPageId, $tableName);

        $previewPageId = $this->getPreviewPageId($currentPageId, $previewConfiguration);
        $languageId = $this->resolveLanguageId($tableName, $record);

        $recordId = $this->resolveRecordIdForLanguageHandling(
            $tableName,
            $record,
            $previewConfiguration,
            $recordId,
            $previewPageId
        );

        $linkParameters = $this->buildLinkParameters($record, $recordId, $previewConfiguration);

        if (!empty($previewConfiguration['useCacheHash'])) {
            $this->addCacheHash($linkParameters, $previewPageId);
        }

        $additionalParams = GeneralUtility::implodeArrayForUrl('', $linkParameters, '', false, true);

        return new RequestData($previewPageId, $languageId, $additionalParams);
    }

    /**
     * @param array<string, mixed> $record
     */
    protected function resolveLanguageId(string $tableName, array $record): int
    {
        $languageField = $GLOBALS['TCA'][$tableName]['ctrl']['languageField'] ?? '';
        if ($languageField === '' || empty($record[$languageField])) {
            return 0;
        }

        return $record[$languageField] > -1 ? (int)$record[$languageField] : 0;
    }

    /**
     * @param array<string, mixed> $record
     * @param array<string, mixed> $config
     */
    protected function resolveRecordIdForLanguageHandling(
        string $tableName,
        array $record,
        array $config,
        int $recordId,
        int &$previewPageId
    ): int {
        $languageField = $GLOBALS['TCA'][$tableName]['ctrl']['languageField'] ?? '';
        if (!$languageField || empty($record[$languageField])) {
            return $recordId;
        }

        $pointerField = $GLOBALS['TCA'][$tableName]['ctrl']['transOrigPointerField'] ?? '';
        if (!$pointerField || empty($record[$pointerField])) {
            return $recordId;
        }

        // Use parent record if configured
        if (
            isset($config['useDefaultLanguageRecord']) &&
            !$config['useDefaultLanguageRecord']
        ) {
            $recordId = (int)$record[$pointerField];
        }

        // Special case for pages
        if ($tableName === TableNames::PAGES) {
            $previewPageId = (int)$record[$pointerField];
        }

        return $recordId;
    }

    /**
     * @param array<string, mixed> $record
     * @param array<string, mixed> $config
     * @return array<int|string, mixed>
     */
    protected function buildLinkParameters(array $record, int $recordId, array $config): array
    {
        $params = [];

        if (!empty($config['fieldToParameterMap.'])) {
            foreach ($config['fieldToParameterMap.'] as $field => $parameterName) {
                $value = $field === 'uid' ? $recordId : ($record[$field] ?? '');
                $params[$parameterName] = $value;
            }
        }

        if (!empty($config['additionalGetParameters.'])) {
            $additional = [];
            $this->parseAdditionalGetParameters($additional, $config['additionalGetParameters.']);
            $params = array_replace($params, $additional);
        }

        return $params;
    }

    /**
     * @param array<int|string, mixed> $params
     */
    protected function addCacheHash(array &$params, int $previewPageId): void
    {
        $queryString = GeneralUtility::implodeArrayForUrl(
            '',
            array_merge($params, ['id' => $previewPageId])
        );

        $cacheHashParams = $this->cacheHashCalculator->getRelevantParameters($queryString);
        $params['cHash'] = $this->cacheHashCalculator->calculateCacheHash($cacheHashParams);
    }

    /**
     * @param array<string, mixed> $previewConfiguration
     */
    protected function getPreviewPageId(int $currentPageId, array $previewConfiguration): int
    {
        $configured = (int)($previewConfiguration['previewPageId'] ?? 0);
        if ($configured > 0) {
            return $configured;
        }

        $rootLine = BackendUtility::BEgetRootLine($currentPageId);
        $currentPage = reset($rootLine);

        if ((int)$currentPage['doktype'] <= PageRepository::DOKTYPE_SPACER) {
            return $currentPageId;
        }

        foreach ($rootLine as $page) {
            if (!empty($page['is_siteroot'])) {
                return (int)$page['uid'];
            }
        }

        return $currentPageId;
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
