<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Form\Element;

use TYPO3\CMS\Backend\Form\AbstractNode;
use TYPO3\CMS\Backend\Form\NodeFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

class Cornerstone extends AbstractNode
{
    public function render(): array
    {
        $resultArray = $this->initializeResultArray();

        $templateView = GeneralUtility::makeInstance(StandaloneView::class);
        $templateView->setTemplatePathAndFilename(
            GeneralUtility::getFileAbsFileName('EXT:yoast_seo/Resources/Private/Templates/TCA/Cornerstone.html')
        );
        $templateView->assign('data', $this->data);

        $resultArray['html'] = $templateView->render();

        return $resultArray;
    }
}
