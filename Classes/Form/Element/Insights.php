<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Form\Element;

use TYPO3\CMS\Backend\Form\AbstractNode;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

class Insights extends AbstractNode
{
    /**
     * @return array<string, mixed>
     */
    public function render(): array
    {
        $resultArray = $this->initializeResultArray();

        $templateView = GeneralUtility::makeInstance(StandaloneView::class);
        $templateView->setPartialRootPaths(
            [GeneralUtility::getFileAbsFileName('EXT:yoast_seo/Resources/Private/Partials/TCA')]
        );
        $templateView->setTemplatePathAndFilename(
            GeneralUtility::getFileAbsFileName(
                'EXT:yoast_seo/Resources/Private/Templates/TCA/Insights.html'
            )
        );

        $templateView->assign('data', $this->data);
        $resultArray['html'] = $templateView->render();
        return $resultArray;
    }
}
