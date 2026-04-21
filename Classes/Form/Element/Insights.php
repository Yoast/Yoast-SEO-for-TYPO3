<?php

/**
 * This file is part of the "yoast_seo" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Form\Element;

use TYPO3\CMS\Backend\Form\AbstractNode;
use TYPO3\CMS\Core\Page\JavaScriptModuleInstruction;
use YoastSeoForTypo3\YoastSeo\Service\Form\NodeTemplateService;

class Insights extends AbstractNode
{
    public function __construct(
        protected NodeTemplateService $templateService
    ) {}

    /**
     * @param array<string, mixed> $data
     */
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
        $resultArray['javaScriptModules'][] = JavaScriptModuleInstruction::create('@yoast/yoast-seo-for-typo3/insights.js');
        return $resultArray;
    }
}
