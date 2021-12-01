<?php
declare(strict_types=1);
namespace YoastSeoForTypo3\YoastSeo\Controller;

use TYPO3\CMS\Backend\View\BackendTemplateView;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;
use YoastSeoForTypo3\YoastSeo\Utility\YoastUtility;

class ModuleController extends ActionController
{
    /**
     * Backend Template Container.
     * Takes care of outer "docheader" and other stuff this module is embedded in.
     *
     * @var string
     */
    protected $defaultViewObjectName = BackendTemplateView::class;

    /**
     * BackendTemplateContainer
     *
     * @var BackendTemplateView
     */
    protected $view;

    /**
     * @var PageRenderer
     */
    protected $pageRenderer;

    /**
     * @param ViewInterface $view
     *
     * @return void
     */
    protected function initializeView(ViewInterface $view): void
    {
        parent::initializeView($view);

        // Early return for actions without valid view like tcaCreateAction or tcaDeleteAction
        if (!($this->view instanceof BackendTemplateView)) {
            return;
        }

        $this->registerDocheaderButtons();
    }

    protected function initializeAction(): void
    {
        parent::initializeAction();
        if (!($this->pageRenderer instanceof PageRenderer)) {
            $this->pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
        }

        $publicResourcesPath = PathUtility::getAbsoluteWebPath(ExtensionManagementUtility::extPath('yoast_seo')) . 'Resources/Public/';

        $this->pageRenderer->addCssFile(
            $publicResourcesPath . 'CSS/yoast-seo-backend.min.css'
        );
    }

    /**
     * @return void
     */
    public function dashboardAction(): void
    {
    }

    public function premiumAction(): void
    {
        $this->view->assignMultiple([
            'features' => [
                ['name' => 'keywords', 'free' => true],
                ['name' => 'preview', 'free' => true],
                ['name' => 'readability', 'free' => true],
                ['name' => 'algorithm', 'free' => true],
                ['name' => 'linking_suggestions', 'free' => false],
                ['name' => 'insights', 'free' => false],
                ['name' => 'advanced_robots', 'free' => false],
                ['name' => 'orphanedcontent', 'free' => false],
                ['name' => 'ad_free', 'free' => false],
                ['name' => 'support', 'free' => false],
            ],
            'upcomingFeatures' => [
                ['name' => 'word_forms', 'free' => false],
                ['name' => 'social_previews', 'free' => false],
            ],
            'premiumInstalled' => YoastUtility::isPremiumInstalled()
        ]);
    }

    /**
     * Registers the Icons into the docheader
     *
     * @return void
     * @throws \InvalidArgumentException
     */
    protected function registerDocheaderButtons(): void
    {
        $buttonBar = $this->view->getModuleTemplate()->getDocHeaderComponent()->getButtonBar();
        $currentRequest = $this->request;
        $moduleName = $currentRequest->getPluginName();
        $shortcutButton = $buttonBar->makeShortcutButton()
            ->setModuleName($moduleName)
            ->setGetVariables(['id' => (int)GeneralUtility::_GP('id')]);
        $buttonBar->addButton($shortcutButton);
    }
}
