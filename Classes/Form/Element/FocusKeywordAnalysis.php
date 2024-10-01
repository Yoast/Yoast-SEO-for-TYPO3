<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Form\Element;

use TYPO3\CMS\Backend\Form\AbstractNode;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use YoastSeoForTypo3\YoastSeo\Service\Form\NodeTemplateService;
use YoastSeoForTypo3\YoastSeo\Utility\YoastUtility;

class FocusKeywordAnalysis extends AbstractNode
{
    // TODO: Use constructor DI when TYPO3 v11 can be dropped
    protected NodeTemplateService $templateService;

    /**
     * @return array<string, mixed>
     */
    public function render(): array
    {
        $this->init();
        $resultArray = $this->initializeResultArray();

        if ($this->data['tableName'] === 'pages'
            && !in_array((int)($this->data['databaseRow']['doktype'][0] ?? 0), YoastUtility::getAllowedDoktypes())) {
            $resultArray['html'] = $this->templateService->renderView('FocusKeywordAnalysis', ['wrongDoktype' => true]);
            return $resultArray;
        }

        if ($focusKeywordField = $this->getFocusKeywordField()) {
            $focusKeywordField = $this->getFieldSelector($focusKeywordField);
        }

        $subtype = '';
        if ($this->data['tableName'] === 'tx_yoastseo_related_focuskeyword') {
            $subtype = 'rk' . $this->data['vanillaUid'];
        }

        $resultArray['html'] = $this->templateService->renderView('FocusKeywordAnalysis', [
            'focusKeywordField' => $focusKeywordField,
            'subtype' => $subtype,
        ]);
        return $resultArray;
    }

    protected function getFocusKeywordField(): ?string
    {
        if (!empty($this->data['parameterArray']['fieldConf']['config']['settings']['focusKeywordField'] ?? '')) {
            return $this->data['parameterArray']['fieldConf']['config']['settings']['focusKeywordField'];
        }
        return null;
    }

    protected function getFieldSelector(string $field): string
    {
        $uid = $this->data['vanillaUid'];

        return 'data[' . $this->data['tableName'] . '][' . $uid . '][' . $field . ']';
    }

    protected function init(): void
    {
        $this->templateService = GeneralUtility::makeInstance(NodeTemplateService::class);
    }
}
