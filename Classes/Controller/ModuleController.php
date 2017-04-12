<?php
namespace YoastSeoForTypo3\YoastSeo\Controller;

use TYPO3\CMS\Backend\Template\Components\ButtonBar;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Backend\View\BackendTemplateView;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Database\DatabaseConnection;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Localization\Locales;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

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
     * @var string
     */
    const FOCUS_KEYWORD_COLUMN_NAME = 'tx_yoastseo_focuskeyword';


    /**
     * @var int
     */
    const FE_PREVIEW_TYPE = 1480321830;

    /**
     * @var string
     */
    const APP_TRANSLATION_FILE_PATTERN = 'EXT:yoast_seo/Resources/Private/Language/wordpress-seo-%s.json';

    /**
     * @var array
     */
    protected $configuration = array(
        'translations' => array(
            'availableLocales' => array(),
            'languageKeyToLocaleMapping' => array()
        )
    );

    /**
     * @var Locales
     */
    protected $localeService;


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
        if (array_key_exists('yoast_seo', $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'])
            && is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['yoast_seo'])
        ) {
            $this->configuration = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['yoast_seo'];
        }

        parent::initializeAction();

        if (!($this->localeService instanceof Locales)) {
            $this->localeService = GeneralUtility::makeInstance(Locales::class);
        }
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
        $languageId = 0;

        if ($this->request->hasArgument('id')) {
            $pageId = $this->request->getArgument('id');
        }
        if ($this->request->hasArgument('language')) {
            $languageId = $this->request->getArgument('language');
        }
        $targetElementId = uniqid('_YoastSEO_panel_');

        $currentPage = BackendUtility::getRecord(
            'pages',
            (int) $pageId
        );

        if ($languageId > 0) {
            $overlayRecords = BackendUtility::getRecordLocalization(
                'pages',
                (int) $pageId,
                (int) $languageId
            );

            if (is_array($overlayRecords) && array_key_exists(0, $overlayRecords) && is_array($overlayRecords[0])) {
                $currentPage = $overlayRecords[0];
            }
        }
        $focusKeyword = $currentPage[self::FOCUS_KEYWORD_COLUMN_NAME];

        $previewDataUrl = vsprintf(
            '/index.php?id=%d&type=%d&L=%d',
            array(
                (int) $pageId,
                self::FE_PREVIEW_TYPE,
                $languageId
            )
        );

        $interfaceLocale = $this->getInterfaceLocale();

        if ($interfaceLocale !== null
            && ($translationFilePath = sprintf(
                self::APP_TRANSLATION_FILE_PATTERN,
                $interfaceLocale
            )) !== false
            && ($translationFilePath = GeneralUtility::getFileAbsFileName(
                $translationFilePath
            )) !== false
            && file_exists($translationFilePath)
        ) {
            $this->pageRenderer->addJsInlineCode(
                md5($translationFilePath),
                'var tx_yoast_seo = tx_yoast_seo || {};'
                . ' tx_yoast_seo.translations = '
                . file_get_contents($translationFilePath)
                . ';'
            );
        }
        $publicResourcesPath = ExtensionManagementUtility::extRelPath('yoast_seo') . 'Resources/Public/';

        $this->pageRenderer->addJsInlineCode(
            'YoastSEO settings',
            'var tx_yoast_seo = tx_yoast_seo || {};'
            . ' tx_yoast_seo.settings = '
            . json_encode(
                array(
                    'focusKeyword' => $focusKeyword,
                    'focusKeywordLabel' => LocalizationUtility::translate('LLL:EXT:yoast_seo/Resources/Private/Language/BackendModule.xlf:focusKeyword', 'yoast_seo'),
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
        $this->view->assign('pageId', $pageId);
        $this->view->assign('targetElementId', $targetElementId);
        $this->view->assign('returnUrl', $returnUrl);
    }

    public function saveAction()
    {
        $pageId = (int)$this->request->getArgument('id');
        $languageId = (int)$this->request->getArgument('language');

        $fields = array();
        $this->addFieldToArray($fields, 'title', 'snippet-editor-title');
        $this->addFieldToArray($fields, 'description', 'snippet-editor-meta-description');
        $this->addFieldToArray($fields, 'tx_yoastseo_facebook_title', 'facebookTitle');
        $this->addFieldToArray($fields, 'tx_yoastseo_facebook_description', 'facebookDescription');
        $this->addFieldToArray($fields, 'tx_yoastseo_twitter_title', 'twitterTitle');
        $this->addFieldToArray($fields, 'tx_yoastseo_twitter_description', 'twitterDescription');
        $this->addFieldToArray($fields, 'tx_yoastseo_canonical_url', 'canonical');
        $this->addFieldToArray($fields, 'tx_yoastseo_focuskeyword', 'focusKeyword');

        $table = 'pages';
        $recordId = $pageId;

        if ($languageId > 0) {
            $table = 'pages_language_overlay';

            $overlayRecords = BackendUtility::getRecordLocalization(
                'pages',
                (int) $pageId,
                (int) $languageId
            );

            if (is_array($overlayRecords) && array_key_exists(0, $overlayRecords) && is_array($overlayRecords[0])) {
                $recordId = $overlayRecords[0]['uid'];
            }

        }
        $data = array(
            $table => array(
                $recordId => $fields
            )
        );

        $tce = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\DataHandling\\DataHandler');
        $tce->reverseOrder = 1;
        $tce->start($data, array());
        $tce->process_datamap();
        \TYPO3\CMS\Backend\Utility\BackendUtility::setUpdateSignal('updatePageTree');
        $tce->clear_cacheCmd('pages');

        $lang = $this->getLanguageService();

        $message = GeneralUtility::makeInstance(
            FlashMessage::class,
            $lang->sL('LLL:EXT:yoast_seo/Resources/Private/Language/BackendModule.xlf:saved.description'),
            $lang->sL('LLL:EXT:yoast_seo/Resources/Private/Language/BackendModule.xlf:saved.title'),
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

        $this->redirect('edit', null, null, array('id' => $pageId, 'language' => $languageId, 'returnUrl' => $returnUrl));
    }

    /**
     * @param $fields
     * @param $key
     * @string $fieldName
     *
     * @return void
     */
    protected function addFieldToArray(&$fields, $key, $fieldName = null)
    {
        if ($fieldName === null) {
            $fieldName = $key;
        }

        if ($this->request->hasArgument($fieldName)) {
            $fields[$key] = $this->request->getArgument($fieldName);
        }
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


    /**
     * Try to resolve a supported locale based on the user settings
     * take the configured locale dependencies into account
     * so if the TYPO3 interface is tailored for a specific dialect
     * the local of a parent language might be used
     *
     * @return string|null
     */
    protected function getInterfaceLocale()
    {
        $locale = null;
        $languageChain = null;

        if ($GLOBALS['BE_USER'] instanceof BackendUserAuthentication
            && is_array($GLOBALS['BE_USER']->uc)
            && array_key_exists('lang', $GLOBALS['BE_USER']->uc)
            && !empty($GLOBALS['BE_USER']->uc['lang'])
        ) {
            $languageChain = $this->localeService->getLocaleDependencies(
                $GLOBALS['BE_USER']->uc['lang']
            );

            array_unshift($languageChain, $GLOBALS['BE_USER']->uc['lang']);
        }

        // try to find a matching locale available for this plugins UI
        // take configured locale dependencies into account
        if ($languageChain !== null
            && ($suitableLocales = array_intersect(
                $languageChain,
                $this->configuration['translations']['availableLocales']
            )) !== false
            && count($suitableLocales) > 0
        ) {
            $locale = array_shift($suitableLocales);
        }

        // if a locale couldn't be resolved try if an entry of the
        // language dependency chain matches legacy mapping
        if ($locale === null && $languageChain !== null
            && ($suitableLanguageKeys = array_intersect(
                $languageChain,
                array_flip(
                    $this->configuration['translations']['languageKeyToLocaleMapping']
                )
            )) !== false
            && count($suitableLanguageKeys) > 0
        ) {
            $locale = $this->configuration['translations']['languageKeyToLocaleMapping'][array_shift($suitableLanguageKeys)];
        }

        return $locale;
    }
}
