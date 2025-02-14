<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Service\Form;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

class NodeTemplateService
{
    /**
     * @param array<string, mixed> $data
     */
    public function renderView(string $template, array $data = []): string
    {
        $templateView = $this->getStandaloneView();
        $templateView->setPartialRootPaths(
            [GeneralUtility::getFileAbsFileName('EXT:yoast_seo/Resources/Private/Partials/TCA')]
        );
        $templateView->setTemplatePathAndFilename(
            GeneralUtility::getFileAbsFileName('EXT:yoast_seo/Resources/Private/Templates/TCA/' . $template . '.html')
        );
        $templateView->assignMultiple($data);
        return $templateView->render();
    }

    protected function getStandaloneView(): StandaloneView
    {
        return GeneralUtility::makeInstance(StandaloneView::class);
    }
}
