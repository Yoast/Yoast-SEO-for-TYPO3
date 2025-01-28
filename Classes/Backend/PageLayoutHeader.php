<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Backend;

use TYPO3\CMS\Backend\Controller\PageLayoutController;
use TYPO3\CMS\Backend\Template\ModuleTemplate;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use YoastSeoForTypo3\YoastSeo\Service\PageLayoutHeader\PageDataService;
use YoastSeoForTypo3\YoastSeo\Service\PageLayoutHeader\PageLayoutHeaderRenderer;
use YoastSeoForTypo3\YoastSeo\Service\PageLayoutHeader\VisibilityChecker;
use YoastSeoForTypo3\YoastSeo\Service\SnippetPreview\SnippetPreviewConfigurationBuilder;
use YoastSeoForTypo3\YoastSeo\Service\SnippetPreviewService;
use YoastSeoForTypo3\YoastSeo\Service\UrlService;

class PageLayoutHeader
{
    public function __construct(
        protected UrlService $urlService,
        protected SnippetPreviewService $snippetPreviewService,
        protected SnippetPreviewConfigurationBuilder $snippetPreviewConfigurationBuilder,
        protected PageLayoutHeaderRenderer $pageLayoutHeaderRenderer,
        protected VisibilityChecker $visibilityChecker,
        protected PageDataService $pageDataService
    ) {}

    /**
     * @param array<string, string>|null $params
     */
    public function render(?array $params = null, PageLayoutController|ModuleTemplate|null $parentObj = null): string
    {
        $languageId = $this->getLanguageId();
        $pageId = (int)$_GET['id'];
        $currentPage = $this->pageDataService->getCurrentPage($pageId, $languageId, $parentObj);

        if (!is_array($currentPage) || !$this->visibilityChecker->shouldShowPreview($pageId, $currentPage)) {
            return '';
        }

        $this->snippetPreviewService->buildSnippetPreview(
            $this->urlService->getPreviewUrl($pageId, $languageId),
            $currentPage,
            $this->snippetPreviewConfigurationBuilder->buildConfigurationForPage($pageId, $currentPage, $languageId)
        );

        return $this->pageLayoutHeaderRenderer->render();
    }

    protected function getLanguageId(): int
    {
        $moduleData = (array)BackendUtility::getModuleData(['language'], [], 'web_layout');
        return (int)$moduleData['language'];
    }
}
