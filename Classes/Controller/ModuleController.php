<?php
namespace YoastSeoForTypo3\YoastSeo\Controller;

use TYPO3\CMS\Backend\Template\Components\ButtonBar;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Backend\View\BackendTemplateView;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Localization\Locales;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Resource\ResourceStorage;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use YoastSeoForTypo3\YoastSeo\Utility\ConvertUtility;

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
    protected $MOD_MENU;

    /**
     * @var array
     */
    protected $configuration = array(
        'translations' => array(
            'availableLocales' => array(),
            'languageKeyToLocaleMapping' => array()
        ),
        'menuActions' => array(),
        'previewDomain' => null,
        'previewUrlTemplate' => '',
        'viewSettings' => array()
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

        $this->createMenu();
        $this->registerDocheaderButtons();
    }

    protected function initializeAction()
    {
        if (array_key_exists('yoast_seo', $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'])
            && is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['yoast_seo'])
        ) {
            ArrayUtility::mergeRecursiveWithOverrule($this->configuration, $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['yoast_seo']);
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
    public function dashboardAction()
    {
    }

    /**
     *
     */
    public function updateAction()
    {

    }

    /**
     *
     */
    public function doConvertAction()
    {
        ConvertUtility::convert();

        $flashMessageService = $this->objectManager->get(\TYPO3\CMS\Core\Messaging\FlashMessageService::class);
        $messageQueue = $flashMessageService->getMessageQueueByIdentifier();
        $lang = $this->getLanguageService();
        $llPrefix = 'LLL:EXT:yoast_seo/Resources/Private/Language/BackendModule.xlf:';

        $messageFileUpload = GeneralUtility::makeInstance(
            FlashMessage::class,
            $lang->sL($llPrefix . 'update.data.convert.success.description'),
            $lang->sL($llPrefix . 'update.data.convert.success'),
            FlashMessage::OK,
            true
        );
        $messageQueue->addMessage($messageFileUpload);
        $this->redirect('update');
    }

    /**
     * @return void
     */
    public function settingsAction()
    {
    }

    public function saveSettingsAction()
    {
        $pageId = (int)$this->request->getArgument('id');
        $languageId = (int)$this->request->getArgument('language');
        $lang = $this->getLanguageService();

        if ($this->request->hasArgument('twitterImage')) {
            $twitterImage = $this->request->getArgument('twitterImage');

            if ($this->request->hasArgument('twitterDeleteImage') ||
                (array_key_exists('tmp_name', $twitterImage) && !empty($twitterImage['tmp_name']))) {
                $GLOBALS['TYPO3_DB']->exec_UPDATEquery('sys_file_reference', 'fieldname="tx_yoastseo_settings_twitter_image" AND uid_foreign=' . $pageId, ['deleted' => 1]);
            }
            if (array_key_exists('tmp_name', $twitterImage) && !empty($twitterImage['tmp_name'])) {
                $resourceFactory = \TYPO3\CMS\Core\Resource\ResourceFactory::getInstance();
                $storage = $resourceFactory->getDefaultStorage();

                if ($storage instanceof ResourceStorage) {
                    $newFile = $storage->addFile(
                        $twitterImage['tmp_name'],
                        $storage->getDefaultFolder(),
                        $twitterImage['name']
                    );

                    $newId = 'NEW1234';
                    $extraTableRecords['sys_file_reference'][$newId] = [
                        'table_local' => 'sys_file',
                        'uid_local' => $newFile->getUid(),
                        'tablenames' => 'tx_yoastseo_settings',
                        'uid_foreign' => $pageId,
                        'fieldname' => 'tx_yoastseo_settings_twitter_image',
                        'pid' => $pageId
                    ];
                }
            }
        }

        if ($this->request->hasArgument('facebookImage')) {
            $facebookImage = $this->request->getArgument('facebookImage');

            if ($this->request->hasArgument('facebookDeleteImage') ||
                (array_key_exists('tmp_name', $facebookImage) && !empty($facebookImage['tmp_name']))) {
                $GLOBALS['TYPO3_DB']->exec_UPDATEquery('sys_file_reference', 'fieldname="tx_yoastseo_settings_facebook_image" AND uid_foreign=' . $pageId, ['deleted' => 1]);
            }
            if (array_key_exists('tmp_name', $facebookImage) && !empty($facebookImage['tmp_name'])) {
                $resourceFactory = \TYPO3\CMS\Core\Resource\ResourceFactory::getInstance();
                $storage = $resourceFactory->getDefaultStorage();

                if ($storage instanceof ResourceStorage) {
                    $newFile = $storage->addFile(
                        $facebookImage['tmp_name'],
                        $storage->getDefaultFolder(),
                        $facebookImage['name']
                    );

                    $newId = 'NEW1234';
                    $extraTableRecords['sys_file_reference'][$newId] = [
                        'table_local' => 'sys_file',
                        'uid_local' => $newFile->getUid(),
                        'tablenames' => 'tx_yoastseo_settings',
                        'uid_foreign' => $pageId,
                        'fieldname' => 'tx_yoastseo_settings_facebook_image',
                        'pid' => $pageId
                    ];
                }
            }
        }

        $tce = GeneralUtility::makeInstance(DataHandler::class);
        $tce->reverseOrder = 1;
        $tce->start($extraTableRecords, array());
        $tce->process_datamap();
        $tce->clear_cacheCmd('pages');

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

        $this->redirect('settings', null, null, array('id' => $pageId, 'language' => $languageId));
    }

    /**
     * @param array $fields
     * @param string $key
     * @param string $fieldName
     * @param array $replace
     *
     * @return void
     */
    protected function addFieldToArray(&$fields, $key, $fieldName = null, $replace = [])
    {
        if ($fieldName === null) {
            $fieldName = $key;
        }

        if ($this->request->hasArgument($fieldName)) {
            $value = $this->request->getArgument($fieldName);

            if (!empty($replace)) {
                $value = preg_replace($replace[0], $replace[1], $value);
            }
            $fields[$key] = $value;
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
                if ($currentRequest->hasArgument('returnUrl') &&
                    $currentRequest->getArgument('returnUrl')) {
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
            if ($currentRequest->getControllerActionName() === 'settings') {
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
        }
        $shortcutButton = $buttonBar->makeShortcutButton()
            ->setModuleName($moduleName)
            ->setDisplayName($shortcutName)
            ->setGetVariables(array('id' => (int)GeneralUtility::_GP('id')));
        $buttonBar->addButton($shortcutButton);
    }

    /**
     * Create menu
     *
     */
    protected function createMenu()
    {
        $lang = $this->getLanguageService();
        $uriBuilder = $this->objectManager->get(UriBuilder::class);
        $uriBuilder->setRequest($this->request);
        $menu = $this->view->getModuleTemplate()->getDocHeaderComponent()->getMenuRegistry()->makeMenu();
        $menu->setIdentifier('yoast-seo');
        $actions = $this->configuration['menuActions'];

        foreach ($actions as $action) {
            $item = $menu->makeMenuItem()
                ->setTitle($lang->sL('LLL:EXT:yoast_seo/Resources/Private/Language/BackendModule.xlf:action.' . $action['label']))
                ->setHref($uriBuilder->reset()->uriFor($action['action'], [], 'Module'))
                ->setActive($this->request->getControllerActionName() === $action['action']);

            $menu->addMenuItem($item);
        }
        $this->view->getModuleTemplate()->getDocHeaderComponent()->getMenuRegistry()->addMenu($menu);
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

    /**
     * Returns the current BE user.
     *
     * @return \TYPO3\CMS\Core\Authentication\BackendUserAuthentication
     */
    protected function getBackendUser()
    {
        return $GLOBALS['BE_USER'];
    }
}
