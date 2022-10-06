<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Form\Element;

use TYPO3\CMS\Backend\Form\AbstractNode;

class RelatedKeyphrases extends AbstractNode
{
    public function render(): array
    {
        $resultArray = $this->initializeResultArray();
        $resultArray['html'] = '<div><a href="#" data-yoast-modal-type="related-keyphrases" class="btn btn-default">Add related keyphrases</a></div>';
        return $resultArray;
    }
}
