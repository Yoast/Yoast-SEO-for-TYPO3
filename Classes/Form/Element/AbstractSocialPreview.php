<?php
declare(strict_types=1);
namespace YoastSeoForTypo3\YoastSeo\Form\Element;

use TYPO3\CMS\Backend\Form\AbstractNode;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;

abstract class AbstractSocialPreview extends AbstractNode
{
    public function render(): array
    {
        $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
        $pageRenderer->loadRequireJsModule('TYPO3/CMS/YoastSeo/yoastModal');
        $resultArray = $this->initializeResultArray();
        $resultArray['html'] = '<div><a href="#" data-yoast-modal-type="social-preview" data-yoast-social-type="' . $this->getSocialType() . '" class="btn btn-default">Get ' . $this->getSocialType() . ' preview</a></div>';
        return $resultArray;
    }

    abstract protected function getSocialType(): string;
}
