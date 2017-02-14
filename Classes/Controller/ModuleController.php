<?php
namespace YoastSeoForTypo3\YoastSeo\Controller;

use TYPO3\CMS\Backend\Controller\PageLayoutController;
use TYPO3\CMS\Backend\Template\Components\ButtonBar;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Backend\View\BackendTemplateView;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Database\DatabaseConnection;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;


class ModuleController extends ActionController {

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
     * @var string
     */
    const FOCUS_KEYWORD_COLUMN_NAME = 'tx_yoastseo_focuskeyword';


    /**
     * @var int
     */
    const FE_PREVIEW_TYPE = 1480321830;

    /**
     * @param ViewInterface $view
     *
     * @return void
     */
    protected function initializeView(ViewInterface $view)
    {
        parent::initializeView($view);

        // Early return for actions without valid view like tcaCreateAction or tcaDeleteAction
        if (!($this->view instanceof BackendTemplateView)) {
            return;
        }

        $this->makeLanguageMenu();
        $this->registerDocheaderButtons();
    }

    protected function initializeAction()
    {
        parent::initializeAction();

        if (!($this->pageRenderer instanceof PageRenderer)) {
            $this->pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
        }
    }

    /**
     * @return void
     */
    public function editAction()
    {
        $pageId = 0;
        if ($this->request->hasArgument('id')) {
            $pageId = $this->request->getArgument('id');
        }
        $targetElementId = uniqid('_YoastSEO_panel_');

        $currentPage = BackendUtility::getRecord(
            'pages',
            (int) $pageId
        );

        $focusKeyword = $currentPage[self::FOCUS_KEYWORD_COLUMN_NAME];

        $previewDataUrl = vsprintf(
            '/index.php?id=%d&type=%d&L=%d',
            array(
                (int) $pageId,
                self::FE_PREVIEW_TYPE,
                0
            )
        );


        $publicResourcesPath = ExtensionManagementUtility::extRelPath('yoast_seo') . 'Resources/Public/';

        $this->pageRenderer->addJsInlineCode(
            'YoastSEO settings',
            'var tx_yoast_seo = tx_yoast_seo || {};'
            . ' tx_yoast_seo.settings = '
            . json_encode(
                array(
                    'focusKeyword' => $focusKeyword,
                    'preview' => $previewDataUrl,
                    'recordId' => '',
                    'recordTable' => '',
                    'targetElementId' => $targetElementId,
                    'editable' => 1
                )
            )
            . ';'
        );

        $this->pageRenderer->addRequireJsConfiguration(
            array(
                'paths' => array(
                    'YoastSEO' => $publicResourcesPath . 'JavaScript/'
                )
            )
        );

        $this->pageRenderer->loadRequireJsModule('YoastSEO/app');

        $this->pageRenderer->addCssFile(
            $publicResourcesPath . 'CSS/yoast-seo.min.css'
        );

        $returnUrl = '';
        if ($this->request->hasArgument('returnUrl')) {
            $returnUrl = $this->request->getArgument('returnUrl');
        }

        $this->view->assign('page', $currentPage);
        $this->view->assign('targetElementId', $targetElementId);
        $this->view->assign('returnUrl', $returnUrl);
    }

    public function saveAction()
    {
        $pageId = (int)$this->request->getArgument('id');

        $data = array(
            'pages' => array(
                $pageId => array(
                    'title' => $this->request->getArgument('snippet-editor-title'),
                    'description' => $this->request->getArgument('snippet-editor-meta-description'),
                    'tx_realurl_pathsegment' => $this->request->getArgument('snippet-editor-slug'),
                    'tx_yoastseo_facebook_title' => $this->request->getArgument('facebookTitle'),
                    'tx_yoastseo_facebook_description' => $this->request->getArgument('facebookDescription'),
                    'tx_yoastseo_twitter_title' => $this->request->getArgument('twitterTitle'),
                    'tx_yoastseo_twitter_description' => $this->request->getArgument('twitterDescription'),
                    'tx_yoastseo_canonical_url' => $this->request->getArgument('canonical'),
                    'tx_yoastseo_focuskeyword' => $this->request->getArgument('focusKeyword'),
                )
            )
        );

        $tce = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\DataHandling\\DataHandler');
        $tce->reverseOrder = 1;
        $tce->start($data, array());
        $tce->process_datamap();
        \TYPO3\CMS\Backend\Utility\BackendUtility::setUpdateSignal('updatePageTree');
        $tce->clear_cacheCmd('pages');


        $message = GeneralUtility::makeInstance(
            FlashMessage::class,
            'The information is saved',
            'Saved',
            FlashMessage::OK,
            true
        );

        $flashMessageService = $this->objectManager->get(\TYPO3\CMS\Core\Messaging\FlashMessageService::class);
        $messageQueue = $flashMessageService->getMessageQueueByIdentifier();
        $messageQueue->addMessage($message);


        $returnUrl = '';
        if ($this->request->hasArgument('returnUrl')) {
            $returnUrl = $this->request->getArgument('returnUrl');
        }

        $this->redirect('edit', null, null, array('id' => $pageId, 'returnUrl' => $returnUrl));
    }

    /**
     * Registers the Icons into the docheader
     *
     * @return void
     * @throws \InvalidArgumentException
     */
    protected function registerDocheaderButtons()
    {
        /** @var ButtonBar $buttonBar */
        $buttonBar = $this->view->getModuleTemplate()->getDocHeaderComponent()->getButtonBar();
        $currentRequest = $this->request;
        $moduleName = $currentRequest->getPluginName();
        $lang = $this->getLanguageService();

        $extensionName = $currentRequest->getControllerExtensionName();
        $modulePrefix = strtolower('tx_' . $extensionName . '_' . $moduleName);
        $shortcutName = $this->getLanguageService()->sL('LLL:EXT:beuser/Resources/Private/Language/locallang.xml:backendUsers');
        if ($currentRequest->getControllerName() === 'Module') {
            if ($currentRequest->getControllerActionName() === 'edit') {
                if ($currentRequest->hasArgument('returnUrl')) {
                    // CLOSE button:
                    $closeButton = $buttonBar->makeLinkButton()
                        ->setHref(urldecode($currentRequest->getArgument('returnUrl')))
                        ->setClasses('t3js-editform-close')
                        ->setTitle($lang->sL('LLL:EXT:lang/locallang_core.xlf:rm.closeDoc'))
                        ->setIcon($this->view->getModuleTemplate()->getIconFactory()->getIcon(
                            'actions-document-close',
                            Icon::SIZE_SMALL
                        ));
                    $buttonBar->addButton($closeButton, ButtonBar::BUTTON_POSITION_LEFT, 1);
                }

                // SAVE button:
                $saveButton = $buttonBar->makeInputButton()
                    ->setTitle($lang->sL('LLL:EXT:lang/locallang_core.xlf:rm.saveDoc'))
                    ->setName($modulePrefix . '[submit]')
                    ->setValue('Save')
                    ->setForm('editYoastSettings')
                    ->setIcon($this->view->getModuleTemplate()->getIconFactory()->getIcon(
                        'actions-document-save',
                        Icon::SIZE_SMALL
                    ))
                    ->setShowLabelText(true);


                $buttonBar->addButton($saveButton, ButtonBar::BUTTON_POSITION_LEFT, 2);
            }
            if ($currentRequest->getControllerActionName() === 'online') {
                $shortcutName = $this->getLanguageService()->sL('LLL:EXT:beuser/Resources/Private/Language/locallang.xml:onlineUsers');
            }
        }
        $shortcutButton = $buttonBar->makeShortcutButton()
            ->setModuleName($moduleName)
            ->setDisplayName($shortcutName)
            ->setGetVariables(array('id' => (int)GeneralUtility::_GP('id')));
        $buttonBar->addButton($shortcutButton);
    }

    /**
     * Make the LanguageMenu
     *
     * @return void
     */
    protected function makeLanguageMenu()
    {
        if (count($this->MOD_MENU['language']) > 1) {
            $lang = $this->getLanguageService();
            $languageMenu = $this->moduleTemplate->getDocHeaderComponent()->getMenuRegistry()->makeMenu();
            $languageMenu->setIdentifier('languageMenu');
            $languageMenu->setLabel($lang->sL('LLL:EXT:lang/locallang_general.xlf:LGL.language', true));
            foreach ($this->MOD_MENU['language'] as $key => $language) {
                $menuItem = $languageMenu
                    ->makeMenuItem()
                    ->setTitle($language)
                    ->setHref(BackendUtility::getModuleUrl($this->moduleName) . '&id=' . $this->id . '&SET[language]=' . $key);
                if ((int)$this->current_sys_language === $key) {
                    $menuItem->setActive(true);
                }
                $languageMenu->addMenuItem($menuItem);
            }
            $this->moduleTemplate->getDocHeaderComponent()->getMenuRegistry()->addMenu($languageMenu);
        }
    }

    /**
     * @return DatabaseConnection
     */
    protected function getDatabaseConnection()
    {
        return $GLOBALS['TYPO3_DB'];
    }


    /**
     * Returns LanguageService
     *
     * @return \TYPO3\CMS\Lang\LanguageService
     */
    protected function getLanguageService()
    {
        return $GLOBALS['LANG'];
    }
}