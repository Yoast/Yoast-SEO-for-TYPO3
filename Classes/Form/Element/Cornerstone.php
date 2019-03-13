<?php
namespace YoastSeoForTypo3\YoastSeo\Form\Element;

use TYPO3\CMS\Backend\Form\AbstractNode;
use TYPO3\CMS\Backend\Form\NodeFactory;
use TYPO3\CMS\Core\Localization\Locales;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;
use YoastSeoForTypo3\YoastSeo\Utility\JsonConfigUtility;
use YoastSeoForTypo3\YoastSeo\Utility\YoastUtility;

class Cornerstone extends AbstractNode
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

        $this->localeService = GeneralUtility::makeInstance(Locales::class);

        $this->templateView = GeneralUtility::makeInstance(StandaloneView::class);
        $this->templateView->setTemplatePathAndFilename(
            GeneralUtility::getFileAbsFileName('EXT:yoast_seo/Resources/Private/Templates/TCA/Cornerstone.html')
        );
    }

    public function render()
    {
        $resultArray = $this->initializeResultArray();

        $this->templateView->assign('data', $this->data);

        $resultArray['html'] = $this->templateView->render();

        $config['fields']['cornerstone'] = 'test123test';
        $jsonConfigUtility = GeneralUtility::makeInstance(JsonConfigUtility::class);
        $jsonConfigUtility->addConfig($config);

        $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
        $pageRenderer->addJsInlineCode('yoast-json-config', $jsonConfigUtility->render());

        return $resultArray;
    }
}
