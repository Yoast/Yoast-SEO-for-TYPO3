<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Form\Element;

use TYPO3\CMS\Backend\Form\AbstractNode;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use YoastSeoForTypo3\YoastSeo\Service\Form\NodeTemplateService;

class Insights extends AbstractNode
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
        $resultArray['html'] = $this->templateService->renderView('Insights', ['data' => $this->data]);
        return $resultArray;
    }

    protected function init(): void
    {
        $this->templateService = GeneralUtility::makeInstance(NodeTemplateService::class);
    }
}
