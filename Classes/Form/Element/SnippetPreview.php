<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Form\Element;

use TYPO3\CMS\Backend\Form\AbstractNode;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use YoastSeoForTypo3\YoastSeo\Service\Form\NodeTemplateService;
use YoastSeoForTypo3\YoastSeo\Service\LocaleService;
use YoastSeoForTypo3\YoastSeo\Service\SnippetPreview\SnippetPreviewConfigurationBuilder;
use YoastSeoForTypo3\YoastSeo\Service\SnippetPreview\SnippetPreviewUrlGenerator;
use YoastSeoForTypo3\YoastSeo\Service\SnippetPreviewService;
use YoastSeoForTypo3\YoastSeo\Utility\YoastUtility;

class SnippetPreview extends AbstractNode
{
    // TODO: Use constructor DI when TYPO3 v11 can be dropped
    protected NodeTemplateService $templateService;
    protected SnippetPreviewConfigurationBuilder $configurationBuilder;
    protected SnippetPreviewUrlGenerator $urlGenerator;
    protected SnippetPreviewService $snippetPreviewService;

    protected string $previewUrl = '';
    protected int $languageId = 0;

    /**
     * @return array<string, mixed>
     */
    public function render(): array
    {
        $this->initialize();

        $resultArray = $this->initializeResultArray();
        $resultArray['stylesheetFiles'][] = 'EXT:yoast_seo/Resources/Public/CSS/yoast.min.css';

        if ($this->data['tableName'] === 'pages'
            && !in_array((int)($this->data['databaseRow']['doktype'][0] ?? 0), YoastUtility::getAllowedDoktypes(), true)) {
            $resultArray['html'] = $this->templateService->renderView('SnippetPreview', ['wrongDoktype' => true]);
            return $resultArray;
        }

        $snippetPreviewConfiguration = $this->configurationBuilder->buildConfigurationForTCA($this->data, $this->languageId);

        $this->snippetPreviewService->buildSnippetPreview($this->previewUrl, $this->data['databaseRow'], $snippetPreviewConfiguration);

        $resultArray['html'] = $this->templateService->renderView('SnippetPreview', [
            'previewUrl' => $this->previewUrl,
            'previewTargetId' => $this->data['fieldName'],
            'titleFieldSelector' => $snippetPreviewConfiguration['fieldSelectors']['title'],
            'descriptionFieldSelector' => $snippetPreviewConfiguration['fieldSelectors']['description'],
            'databaseRow' => $this->data['databaseRow'],
            'focusKeyword' => YoastUtility::getFocusKeywordOfRecord(
                (int)$this->data['databaseRow']['uid'],
                $this->data['tableName']
            ),
            'vanillaUid' => $this->data['vanillaUid'],
            'tableName' => $this->data['tableName'],
            'languageId' => $this->languageId,
        ]);

        return $resultArray;
    }

    protected function initialize(): void
    {
        $this->templateService = GeneralUtility::makeInstance(NodeTemplateService::class);
        $this->configurationBuilder = GeneralUtility::makeInstance(SnippetPreviewConfigurationBuilder::class);
        $this->urlGenerator = GeneralUtility::makeInstance(SnippetPreviewUrlGenerator::class);
        $this->snippetPreviewService = GeneralUtility::makeInstance(SnippetPreviewService::class);

        $this->previewUrl = $this->urlGenerator->getPreviewUrl($this->data);
        $this->languageId = GeneralUtility::makeInstance(LocaleService::class)->getLanguageIdFromData($this->data);
    }
}
