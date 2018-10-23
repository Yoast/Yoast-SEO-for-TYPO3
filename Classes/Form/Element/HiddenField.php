<?php
namespace YoastSeoForTypo3\YoastSeo\Form\Element;

use TYPO3\CMS\Backend\Form\AbstractNode;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

class HiddenField extends AbstractNode
{
    /**
     * @var StandaloneView
     */
    protected $templateView;

    public function render()
    {
        $parameterArray = $this->data['parameterArray'];
        $resultArray = $this->initializeResultArray();

//        DebuggerUtility::var_dump($resultArray);
        $resultArray['html'] = '<input class="yoastHiddenTcaField" type="text" name="' . $parameterArray['itemFormElName'] . '" value="' . htmlspecialchars($parameterArray['itemFormElValue']) . '" />';
        return $resultArray;
    }
}
