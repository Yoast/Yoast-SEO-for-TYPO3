<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Backend;

use TYPO3\CMS\Backend\Controller\PageLayoutController;
use TYPO3\CMS\Backend\Template\ModuleTemplate;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Utility\GeneralUtility;
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
        if (GeneralUtility::makeInstance(Typo3Version::class)->getMajorVersion() === 11) {
            $moduleData = BackendUtility::getModuleData(['language'], [], 'web_layout');
            return (int)$moduleData['language'];
        }

        $request = $GLOBALS['TYPO3_REQUEST'] ?? null;
        if ($request === null) {
            return 0;
        }

        $moduleData = $request->getAttribute('moduleData');
        if ($moduleData === null) {
            return 0;
        }

        $language = (int)$moduleData->get('language');
        if ($language === -1) {
            return 0;
        }

        return $language;
    }
}
