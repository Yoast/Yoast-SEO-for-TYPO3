<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Service\PageLayoutHeader;

use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

class PageLayoutHeaderRenderer
{
    public function render(): string
    {
        $templateView = $this->getStandaloneView();
        $templateView->setTemplatePathAndFilename(
            GeneralUtility::getFileAbsFileName('EXT:yoast_seo/Resources/Private/Templates/PageLayout/Header.html')
        );
        $templateView->assignMultiple([
            'targetElementId' => uniqid('_YoastSEO_panel_'),
        ]);
        /** @var PageRenderer $pageRenderer */
        $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
        $pageRenderer->loadJavaScriptModule('@yoast/yoast-seo-for-typo3/dist/webcomponents.js');
        return $templateView->render();
    }

    protected function getStandaloneView(): StandaloneView
    {
        return GeneralUtility::makeInstance(StandaloneView::class);
    }
}
