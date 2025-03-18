<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Form\Element;

use TYPO3\CMS\Backend\Form\AbstractNode;
use YoastSeoForTypo3\YoastSeo\Service\Form\NodeTemplateService;

class Insights extends AbstractNode
{
    public function __construct(
        protected NodeTemplateService $templateService
    ) {}

    public function setData(array $data): void
    {
        $this->data = $data;
    }

    /**
     * @return array<string, mixed>
     */
    public function render(): array
    {
        $resultArray = $this->initializeResultArray();
        $resultArray['html'] = $this->templateService->renderView('Insights', ['data' => $this->data]);
        return $resultArray;
    }
}
