<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Form\Element;

use TYPO3\CMS\Backend\Form\AbstractNode;
use YoastSeoForTypo3\YoastSeo\Service\Form\NodeTemplateService;
use YoastSeoForTypo3\YoastSeo\Service\LocaleService;
use YoastSeoForTypo3\YoastSeo\Service\SnippetPreview\SnippetPreviewConfigurationBuilder;
use YoastSeoForTypo3\YoastSeo\Service\SnippetPreview\SnippetPreviewRequestDataGenerator;
use YoastSeoForTypo3\YoastSeo\Service\SnippetPreviewService;
use YoastSeoForTypo3\YoastSeo\Utility\YoastUtility;

class SnippetPreview extends AbstractNode
{
    public function __construct(
        protected NodeTemplateService $templateService,
        protected SnippetPreviewConfigurationBuilder $configurationBuilder,
        protected SnippetPreviewRequestDataGenerator $requestDataGenerator,
        protected SnippetPreviewService $snippetPreviewService,
        protected LocaleService $localeService,
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
        if ($this->data['command'] === 'new') {
            return [];
        }

        $resultArray = $this->initializeResultArray();
        $resultArray['stylesheetFiles'][] = 'EXT:yoast_seo/Resources/Public/CSS/yoast.min.css';

        if ($this->data['tableName'] === 'pages' && !in_array(
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

        $resultArray['html'] = $this->templateService->renderView('SnippetPreview');

        return $resultArray;
    }
}
