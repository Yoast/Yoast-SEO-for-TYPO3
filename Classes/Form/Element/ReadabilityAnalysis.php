<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Form\Element;

use TYPO3\CMS\Backend\Form\AbstractNode;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use YoastSeoForTypo3\YoastSeo\Service\Form\NodeTemplateService;
use YoastSeoForTypo3\YoastSeo\Utility\YoastUtility;

class ReadabilityAnalysis extends AbstractNode
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
            $resultArray['html'] = $this->templateService->renderView('ReadabilityAnalysis', ['wrongDoktype' => true]);
            return $resultArray;
        }

        $resultArray['html'] = $this->templateService->renderView('ReadabilityAnalysis');
        return $resultArray;
    }

    protected function init(): void
    {
        $this->templateService = GeneralUtility::makeInstance(NodeTemplateService::class);
    }
}
