<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Form\Element;

use TYPO3\CMS\Backend\Form\AbstractNode;

class Synonyms extends AbstractNode
{
    public function render(): array
    {
        $resultArray = $this->initializeResultArray();
        $resultArray['html'] = '<div><a href="#" data-yoast-modal-type="synonyms" class="btn btn-default">Add synonyms</a></div>';
        return $resultArray;
    }
}
