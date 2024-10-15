<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Form\Element;

use TYPO3\CMS\Backend\Form\AbstractNode;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;
use YoastSeoForTypo3\YoastSeo\Utility\YoastUtility;

class ReadabilityAnalysis extends AbstractNode
{
    /**
     * @return array<string, mixed>
     */
    public function render(): array
    {
        $resultArray = $this->initializeResultArray();
        $templateView = $this->getTemplateView();

        $allowedDoktypes = YoastUtility::getAllowedDoktypes();
        if ($this->data['tableName'] === 'pages'
            && !\in_array((int)($this->data['databaseRow']['doktype'][0] ?? 0), $allowedDoktypes)) {
            $templateView->assign('wrongDoktype', true);
        }
        $templateView->assign('subtype', '');
        $resultArray['html'] = $templateView->render();
        return $resultArray;
    }

    protected function getTemplateView(): StandaloneView
    {
        $templateView = GeneralUtility::makeInstance(StandaloneView::class);
        $templateView->setPartialRootPaths(
            [GeneralUtility::getFileAbsFileName('EXT:yoast_seo/Resources/Private/Partials/TCA')]
        );
        $templateView->setTemplatePathAndFilename(
            GeneralUtility::getFileAbsFileName('EXT:yoast_seo/Resources/Private/Templates/TCA/ReadabilityAnalysis.html')
        );
        return $templateView;
    }
}
