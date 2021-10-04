<?php
declare(strict_types=1);
namespace YoastSeoForTypo3\YoastSeo\Form\Element;

use TYPO3\CMS\Backend\Form\AbstractNode;
use TYPO3\CMS\Backend\Form\NodeFactory;

class Insights extends AbstractNode
{
    /**
     * @param NodeFactory $nodeFactory
     * @param array $data
     */
    public function __construct(NodeFactory $nodeFactory, array $data)
    {
        parent::__construct($nodeFactory, $data);
    }

    public function render(): array
    {
        $resultArray = $this->initializeResultArray();

        $resultArray['html'] = '<div><a href="#" data-yoast-modal-type="insights" class="btn btn-default">Get insights</a></div>';

        return $resultArray;
    }
}
