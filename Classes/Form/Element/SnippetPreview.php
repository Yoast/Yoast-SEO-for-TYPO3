<?php

/**
 * This file is part of the "yoast_seo" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Form\Element;

use TYPO3\CMS\Core\Page\JavaScriptModuleInstruction;
use YoastSeoForTypo3\YoastSeo\Constants\TableNames;
use YoastSeoForTypo3\YoastSeo\Service\Form\NodeTemplateService;
use YoastSeoForTypo3\YoastSeo\Service\LocaleService;
use YoastSeoForTypo3\YoastSeo\Service\SnippetPreview\SnippetPreviewConfigurationBuilder;
use YoastSeoForTypo3\YoastSeo\Service\SnippetPreview\SnippetPreviewRequestDataGenerator;
use YoastSeoForTypo3\YoastSeo\Service\SnippetPreviewService;
use YoastSeoForTypo3\YoastSeo\Utility\YoastUtility;

class SnippetPreview extends AbstractLabeledFormElement
{
    public function __construct(
        protected NodeTemplateService $templateService,
        protected SnippetPreviewConfigurationBuilder $configurationBuilder,
        protected SnippetPreviewRequestDataGenerator $requestDataGenerator,
        protected SnippetPreviewService $snippetPreviewService,
        protected LocaleService $localeService,
    ) {}

    protected function isSnippetPreviewHidden(): bool
    {
        $value = $this->data['databaseRow']['tx_yoastseo_hide_snippet_preview'] ?? false;
        if (is_array($value)) {
            $value = $value[0] ?? false;
        }

        return (bool)$value;
    }

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
        if ($this->data['command'] === 'new') {
            return [];
        }

        $resultArray = $this->initializeResultArray();
        $resultArray['stylesheetFiles'][] = 'EXT:yoast_seo/Resources/Public/CSS/yoast.min.css';

        if ($this->data['tableName'] === TableNames::PAGES && !in_array(
            (int)($this->data['databaseRow']['doktype'][0] ?? 0),
            YoastUtility::getAllowedDoktypes(),
            true
        )) {
            $resultArray['html'] = $this->templateService->renderView('SnippetPreview', ['wrongDoktype' => true]);
            return $resultArray;
        }

        $snippetPreviewConfiguration = $this->configurationBuilder->buildConfigurationForTCA(
            $this->data,
            $this->localeService->getLanguageIdFromData($this->data)
        );

        $this->snippetPreviewService->buildSnippetPreview(
            $this->requestDataGenerator->getRequestData($this->data),
            $this->data['databaseRow'],
            $snippetPreviewConfiguration
        );

        // Also load the javascript for cornerstone and progress bars
        $resultArray['javaScriptModules'][] = JavaScriptModuleInstruction::create('@yoast/yoast-seo-for-typo3/cornerstone.js');
        $resultArray['javaScriptModules'][] = JavaScriptModuleInstruction::create('@yoast/yoast-seo-for-typo3/title-description.js');

        if ($this->isSnippetPreviewHidden()) {
            return $resultArray;
        }

        $resultArray['html'] = $this->getLabel() . $this->templateService->renderView('SnippetPreview');

        return $resultArray;
    }
}
