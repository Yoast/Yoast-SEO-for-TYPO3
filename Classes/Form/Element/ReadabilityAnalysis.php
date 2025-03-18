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
use YoastSeoForTypo3\YoastSeo\Service\Form\NodeTemplateService;
use YoastSeoForTypo3\YoastSeo\Utility\YoastUtility;

class ReadabilityAnalysis extends AbstractNode
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

        if ($this->data['tableName'] === 'pages' && !in_array(
            (int)($this->data['databaseRow']['doktype'][0] ?? 0),
            YoastUtility::getAllowedDoktypes()
        )) {
            $resultArray['html'] = $this->templateService->renderView('ReadabilityAnalysis', ['wrongDoktype' => true]);
            return $resultArray;
        }

        $resultArray['html'] = $this->templateService->renderView('ReadabilityAnalysis');
        return $resultArray;
    }
}
