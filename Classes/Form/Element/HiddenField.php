<?php
declare(strict_types=1);
namespace YoastSeoForTypo3\YoastSeo\Form\Element;

use TYPO3\CMS\Backend\Form\AbstractNode;
use TYPO3\CMS\Fluid\View\StandaloneView;

class HiddenField extends AbstractNode
{
    /**
     * @var StandaloneView
     */
    protected $templateView;

    public function render(): array
    {
        $parameterArray = $this->data['parameterArray'];
        $resultArray = $this->initializeResultArray();

        $resultArray['html'] = '<input class="yoastHiddenTcaField" type="text" name="' . $parameterArray['itemFormElName'] . '" value="' . htmlspecialchars($parameterArray['itemFormElValue']) . '" />';
        return $resultArray;
    }
}
