<?php
namespace YoastSeoForTypo3\YoastSeo\Form\Element;

use TYPO3\CMS\Backend\Form\AbstractNode;
use TYPO3\CMS\Backend\Form\NodeFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

class FocusKeywordAnalysis extends AbstractNode
{
    /**
     * @var StandaloneView
     */
    protected $templateView;

    /**
     * @var string
     */
    protected $focusKeywordField = '';

    /**
     * @param NodeFactory $nodeFactory
     * @param array $data
     */
    public function __construct(NodeFactory $nodeFactory, array $data)
    {
        parent::__construct($nodeFactory, $data);

        $this->templateView = GeneralUtility::makeInstance(StandaloneView::class);
        $this->templateView->setPartialRootPaths([GeneralUtility::getFileAbsFileName('EXT:yoast_seo/Resources/Private/Partials/TCA')]);
        $this->templateView->setTemplatePathAndFilename(GeneralUtility::getFileAbsFileName('EXT:yoast_seo/Resources/Private/Templates/TCA/FocusKeywordAnalysis.html'));

        if (array_key_exists('focusKeywordField', (array)$this->data['parameterArray']['fieldConf']['config']['settings']) &&
            $this->data['parameterArray']['fieldConf']['config']['settings']['focusKeywordField']
        ) {
            $this->focusKeywordField = $this->data['parameterArray']['fieldConf']['config']['settings']['focusKeywordField'];
        }
    }

    public function render()
    {
        $resultArray = $this->initializeResultArray();

        if ($this->focusKeywordField) {
            $this->templateView->assign('focusKeywordField', $this->getFieldSelector($this->focusKeywordField));
        }

        $resultArray['html'] = $this->templateView->render();
        return $resultArray;
    }


    /**
     * @param string $field
     * @return string
     */
    protected function getFieldSelector($field)
    {
        $uid = $this->data['vanillaUid'];

        return 'data[' . $this->data['tableName'] . '][' . $uid . '][' . $field . ']';
    }
}
