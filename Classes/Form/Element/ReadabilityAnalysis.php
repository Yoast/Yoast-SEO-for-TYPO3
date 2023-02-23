<?php
declare(strict_types=1);
namespace YoastSeoForTypo3\YoastSeo\Form\Element;

use TYPO3\CMS\Backend\Form\AbstractNode;
use TYPO3\CMS\Backend\Form\NodeFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;
use YoastSeoForTypo3\YoastSeo\Utility\YoastUtility;

class ReadabilityAnalysis extends AbstractNode
{
    /**
     * @var StandaloneView
     */
    protected $templateView;

    /**
     * @param NodeFactory $nodeFactory
     * @param array $data
     */
    public function __construct(NodeFactory $nodeFactory, array $data)
    {
        parent::__construct($nodeFactory, $data);

        $this->templateView = GeneralUtility::makeInstance(StandaloneView::class);
        $this->templateView->setPartialRootPaths(
            [GeneralUtility::getFileAbsFileName('EXT:yoast_seo/Resources/Private/Partials/TCA')]
        );
        $this->templateView->setTemplatePathAndFilename(
            GeneralUtility::getFileAbsFileName('EXT:yoast_seo/Resources/Private/Templates/TCA/ReadabilityAnalysis.html')
        );
    }

    public function render(): array
    {
        $resultArray = $this->initializeResultArray();

        $allowedDoktypes = YoastUtility::getAllowedDoktypes();
        if ($this->data['tableName'] === 'pages' && !\in_array((int)$this->data['databaseRow']['doktype'][0] ?? 0, $allowedDoktypes)) {
            $this->templateView->assign('wrongDoktype', true);
        }
        $this->templateView->assign('subtype', '');
        $resultArray['html'] = $this->templateView->render();
        return $resultArray;
    }
}
