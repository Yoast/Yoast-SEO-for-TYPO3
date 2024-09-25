<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Form\Element;

use TYPO3\CMS\Backend\Form\AbstractNode;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;
use YoastSeoForTypo3\YoastSeo\Utility\YoastUtility;

class FocusKeywordAnalysis extends AbstractNode
{
    /**
     * @return array<string, mixed>
     */
    public function render(): array
    {
        $resultArray = $this->initializeResultArray();
        $templateView = $this->getTemplateView();

        if ($focusKeywordField = $this->getFocusKeywordField()) {
            $templateView->assign('focusKeywordField', $this->getFieldSelector($focusKeywordField));
        }

        $allowedDoktypes = YoastUtility::getAllowedDoktypes();
        if ($this->data['tableName'] === 'pages'
            && !in_array((int)($this->data['databaseRow']['doktype'][0] ?? 0), $allowedDoktypes)) {
            $templateView->assign('wrongDoktype', true);
        }
        $subtype = '';
        if ($this->data['tableName'] === 'tx_yoastseo_related_focuskeyword') {
            $subtype = 'rk' . $this->data['vanillaUid'];
        }

        $templateView->assign('subtype', $subtype);
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
            GeneralUtility::getFileAbsFileName(
                'EXT:yoast_seo/Resources/Private/Templates/TCA/FocusKeywordAnalysis.html'
            )
        );
        return $templateView;
    }

    protected function getFocusKeywordField(): ?string
    {
        if (isset($this->data['parameterArray']['fieldConf']['config']['settings']['focusKeywordField'])
            && !empty($this->data['parameterArray']['fieldConf']['config']['settings']['focusKeywordField'])
        ) {
            return $this->data['parameterArray']['fieldConf']['config']['settings']['focusKeywordField'];
        }
        return null;
    }

    protected function getFieldSelector(string $field): string
    {
        $uid = $this->data['vanillaUid'];

        return 'data[' . $this->data['tableName'] . '][' . $uid . '][' . $field . ']';
    }
}
