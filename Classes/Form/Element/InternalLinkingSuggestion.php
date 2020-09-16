<?php
namespace YoastSeoForTypo3\YoastSeo\Form\Element;

use TYPO3\CMS\Backend\Form\AbstractNode;
use TYPO3\CMS\Backend\Form\NodeFactory;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;

class InternalLinkingSuggestion extends AbstractNode
{
    /**
     * @param NodeFactory $nodeFactory
     * @param array $data
     */
    public function __construct(NodeFactory $nodeFactory, array $data)
    {
        parent::__construct($nodeFactory, $data);
    }

    public function render()
    {
        $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);

        $pageRenderer->loadRequireJsModule('TYPO3/CMS/YoastSeo/yoastModal');

        $resultArray = $this->initializeResultArray();

        $resultArray['html'] = '<div><a href="#" data-yoast-modal-type="internal-linking-suggestion" class="btn btn-default">Get internal linking suggestions</a></div>';

        return $resultArray;
    }
}
