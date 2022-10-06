<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Form\Element;

use TYPO3\CMS\Backend\Form\AbstractNode;

class AdvancedRobots extends AbstractNode
{
    public function render(): array
    {
        $resultArray = $this->initializeResultArray();

        $resultArray['html'] = '<div><a href="#" data-yoast-modal-type="advanced-robots" class="btn btn-default">Get advanced robots</a></div>';

        return $resultArray;
    }
}
