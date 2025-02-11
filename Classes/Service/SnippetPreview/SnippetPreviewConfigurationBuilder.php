<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Service\SnippetPreview;

use YoastSeoForTypo3\YoastSeo\Utility\YoastUtility;

class SnippetPreviewConfigurationBuilder
{
    protected string $titleField = 'title';
    protected string $pageTitleField = 'title';
    protected string $descriptionField = 'description';
    protected string $focusKeywordField = 'tx_yoastseo_focuskeyword';
    protected string $focusKeywordSynonymsField = 'tx_yoastseo_focuskeyword_synonyms';
    protected string $cornerstoneField = 'tx_yoastseo_cornerstone';
    protected string $relatedKeyphrases = 'tx_yoastseo_focuskeyword_related';

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function buildConfigurationForTCA(array $data, int $languageId): array
    {
        $this->initializeFields($data);

        return [
            'TCA' => 1,
            'data' => [
                'table' => $data['tableName'],
                'uid' => (int)($data['defaultLanguagePageRow']['uid'] ?? $data['databaseRow']['uid']),
                'pid' => (int)$data['databaseRow']['pid'],
                'languageId' => $languageId,
            ],
            'fieldSelectors' => $this->buildFieldSelectors($data),
            'relatedKeyphrases' => YoastUtility::getRelatedKeyphrases($data['tableName'], (int)$data['databaseRow']['uid']),
        ];
    }

    /**
     * @param array<string, mixed> $currentPage
     * @return array<string, mixed>
     */
    public function buildConfigurationForPage(int $pageId, array $currentPage, int $languageId): array
    {
        return [
            'data' => [
                'table' => 'pages',
                'uid' => $pageId,
                'pid' => $currentPage['pid'],
                'languageId' => $languageId,
            ],
            'fieldSelectors' => [],
        ];
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, string>
     */
    protected function buildFieldSelectors(array $data): array
    {
        return [
            'title' => $this->getFieldSelector($data, $this->titleField),
            'pageTitle' => $this->getFieldSelector($data, $this->pageTitleField),
            'description' => $this->getFieldSelector($data, $this->descriptionField),
            'focusKeyword' => $this->getFieldSelector($data, $this->focusKeywordField),
            'focusKeywordSynonyms' => $this->getFieldSelector($data, $this->focusKeywordSynonymsField),
            'cornerstone' => $this->getFieldSelector($data, $this->cornerstoneField),
            'relatedKeyword' => $this->getFieldSelector($data, $this->relatedKeyphrases, true),
        ];
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function getFieldSelector(array $data, string $field, bool $id = false): string
    {
        if ($id === true) {
            $element = 'data-' . $data['vanillaUid'] . '-' . $data['tableName'] . '-' . $data['vanillaUid'] . '-' . $field;
        } else {
            $element = 'data' . str_replace('tx_yoastseo_snippetpreview', $field, $data['elementBaseName']);
        }

        return $element;
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function initializeFields(array $data): void
    {
        foreach (['titleField', 'pageTitleField', 'descriptionField'] as $field) {
            if (!empty($data['parameterArray']['fieldConf']['config']['settings'][$field] ?? '')) {
                $this->$field = $data['parameterArray']['fieldConf']['config']['settings'][$field];
            }
        }
    }
}
