<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\EventListener;

use TYPO3\CMS\Backend\Controller\Event\ModifyPageLayoutContentEvent;
use YoastSeoForTypo3\YoastSeo\Dto\RequestData;
use YoastSeoForTypo3\YoastSeo\Service\PageLayoutHeader\PageDataService;
use YoastSeoForTypo3\YoastSeo\Service\PageLayoutHeader\PageLayoutHeaderRenderer;
use YoastSeoForTypo3\YoastSeo\Service\PageLayoutHeader\VisibilityChecker;
use YoastSeoForTypo3\YoastSeo\Service\SnippetPreview\SnippetPreviewConfigurationBuilder;
use YoastSeoForTypo3\YoastSeo\Service\SnippetPreviewService;

class ModifyPageLayoutContentListener
{
    public function __construct(
        protected SnippetPreviewService $snippetPreviewService,
        protected SnippetPreviewConfigurationBuilder $snippetPreviewConfigurationBuilder,
        protected PageLayoutHeaderRenderer $pageLayoutHeaderRenderer,
        protected VisibilityChecker $visibilityChecker,
        protected PageDataService $pageDataService
    ) {}

    public function __invoke(ModifyPageLayoutContentEvent $event): void
    {
        $languageId = $this->getLanguageId();
        $pageId = (int)$_GET['id'];
        $currentPage = $this->pageDataService->getCurrentPage($pageId, $languageId, $event->getModuleTemplate());

        if (!is_array($currentPage) || !$this->visibilityChecker->shouldShowPreview($pageId, $currentPage)) {
            return;
        }

        $this->snippetPreviewService->buildSnippetPreview(
            new RequestData($pageId, $languageId),
            $currentPage,
            $this->snippetPreviewConfigurationBuilder->buildConfigurationForPage($pageId, $currentPage, $languageId)
        );

        $event->addHeaderContent($this->pageLayoutHeaderRenderer->render());
    }

    protected function getLanguageId(): int
    {
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
