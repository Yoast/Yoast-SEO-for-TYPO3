<?php
namespace YoastSeoForTypo3\YoastSeo\Form\Element;

use TYPO3\CMS\Backend\Form\AbstractNode;
use TYPO3\CMS\Backend\Form\NodeFactory;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;
use TYPO3\CMS\Frontend\Page\PageRepository;

class SnippetPreview extends AbstractNode
{
    /**
     * @var int
     */
    const FE_PREVIEW_TYPE = 1480321830;

    /**
     * @var StandaloneView
     */
    protected $templateView;

    /**
     * @var string
     */
    protected $titleField = 'title';

    /**
     * @var string
     */
    protected $descriptionField = 'description';

    /**
     * @var string
     */
    protected $table = 'pages';

    /**
     * @var int
     */
    protected $languageId = 0;

    /**
     * @var string
     */
    protected $viewScript = '/index.php?id=';

    /**
     * @param NodeFactory $nodeFactory
     * @param array $data
     */
    public function __construct(NodeFactory $nodeFactory, array $data)
    {
        parent::__construct($nodeFactory, $data);

        $this->templateView = GeneralUtility::makeInstance(StandaloneView::class);
        $this->templateView->setTemplatePathAndFilename(GeneralUtility::getFileAbsFileName('EXT:yoast_seo/Resources/Private/Templates/TCA/SnippetPreview.html'));

        if (array_key_exists('titleField', (array)$this->data['parameterArray']['fieldConf']['config']['settings']) &&
            $this->data['parameterArray']['fieldConf']['config']['settings']['titleField']
        ) {
            $this->titleField = $this->data['parameterArray']['fieldConf']['config']['settings']['titleField'];
        }

        if (array_key_exists('descriptionField', (array)$this->data['parameterArray']['fieldConf']['config']['settings']) &&
            $this->data['parameterArray']['fieldConf']['config']['settings']['descriptionField']
        ) {
            $this->descriptionField = $this->data['parameterArray']['fieldConf']['config']['settings']['descriptionField'];
        }

        if (array_key_exists('0', (array)$this->data['databaseRow']['sys_language_uid']) &&
            $this->data['databaseRow']['sys_language_uid'][0]
        ) {
            $this->languageId = (int)$this->data['databaseRow']['sys_language_uid'][0];
        }

        $this->table = $this->data['tableName'];

        $this->previewUrl = $this->getPreviewUrl();
    }

    public function render()
    {
        $publicResourcesPath = PathUtility::getAbsoluteWebPath('../typo3conf/ext/yoast_seo/Resources/Public/');

        $resultArray = $this->initializeResultArray();

        $this->templateView->assign('previewUrl', $this->previewUrl);
        $this->templateView->assign('previewTargetId', $this->data['fieldName']);
        $this->templateView->assign('titleFieldSelector', $this->getFieldSelector($this->titleField));
        $this->templateView->assign('descriptionFieldSelector', $this->getFieldSelector($this->descriptionField));

        $resultArray['stylesheetFiles'][] = $publicResourcesPath . 'CSS/yoast-seo-tca.min.css';
        $resultArray['requireJsModules'] = ['TYPO3/CMS/YoastSeo/Tca'];
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

        return 'data[' . $this->table . '][' . $uid . '][' . $field . ']';
    }

    /**
     * @return string
     */
    protected function getPreviewUrl()
    {
        $currentPageId = $this->data['effectivePid'];

        $recordArray = BackendUtility::getRecord($this->table, $this->data['vanillaUid']);

        $pageTsConfig = BackendUtility::getPagesTSconfig($currentPageId);
        $previewConfiguration = isset($pageTsConfig['TCEMAIN.']['preview.'][$this->table . '.'])
            ? $pageTsConfig['TCEMAIN.']['preview.'][$this->table . '.']
            : [];


        // find the right preview page id
        $previewPageId = 0;
        if (isset($previewConfiguration['previewPageId'])) {
            $previewPageId = $previewConfiguration['previewPageId'];
        }

        // if no preview page was configured
        if (!$previewPageId) {
            $rootPageData = null;
            $rootLine = BackendUtility::BEgetRootLine((int)$currentPageId);
            $currentPage = reset($rootLine);
            // Allow all doktypes below 200
            // This makes custom doktype work as well with opening a frontend page.
            if ((int)$currentPage['doktype'] <= PageRepository::DOKTYPE_SPACER) {
                // try the current page
                $previewPageId = $currentPageId;
            } else {
                // or search for the root page
                foreach ($rootLine as $page) {
                    if ($page['is_siteroot']) {
                        $rootPageData = $page;
                        break;
                    }
                }
                $previewPageId = isset($rootPageData)
                    ? (int)$rootPageData['uid']
                    : (int)$currentPageId;
            }
        }

        $linkParameters = [
            'no_cache' => 1,
            'type' => self::FE_PREVIEW_TYPE
        ];

        // language handling
        $languageField = isset($GLOBALS['TCA'][$this->table]['ctrl']['languageField'])
            ? $GLOBALS['TCA'][$this->table]['ctrl']['languageField']
            : '';
        if ($languageField && !empty($recordArray[$languageField])) {
            $l18nPointer = isset($GLOBALS['TCA'][$this->table]['ctrl']['transOrigPointerField'])
                ? $GLOBALS['TCA'][$this->table]['ctrl']['transOrigPointerField']
                : '';
            if ($l18nPointer && !empty($recordArray[$l18nPointer])
                && isset($previewConfiguration['useDefaultLanguageRecord'])
                && !$previewConfiguration['useDefaultLanguageRecord']
            ) {
                // use parent record
                $recordId = $recordArray[$l18nPointer];
            }
            $linkParameters['L'] = $recordArray[$languageField];
        }

        // map record data to GET parameters
        if (isset($previewConfiguration['fieldToParameterMap.'])) {
            foreach ($previewConfiguration['fieldToParameterMap.'] as $field => $parameterName) {
                $value = $recordArray[$field];
                if ($field === 'uid') {
                    $value = $recordId;
                }
                $linkParameters[$parameterName] = $value;
            }
        }

        // add/override parameters by configuration
        if (isset($previewConfiguration['additionalGetParameters.'])) {
            $additionalGetParameters = [];
            $this->parseAdditionalGetParameters(
                $additionalGetParameters,
                $previewConfiguration['additionalGetParameters.']
            );
            $linkParameters = array_replace($linkParameters, $additionalGetParameters);
        }

        $previewDomain = BackendUtility::getViewDomain($previewPageId);
        $additionalParamsForUrl = GeneralUtility::implodeArrayForUrl('', $linkParameters, '', false, true);

        return $previewDomain . $this->viewScript . $previewPageId . $additionalParamsForUrl;
    }

    /**
     * Migrates a set of (possibly nested) GET parameters in TypoScript syntax to a plain array
     *
     * This basically removes the trailing dots of sub-array keys in TypoScript.
     * The result can be used to create a query string with GeneralUtility::implodeArrayForUrl().
     *
     * @param array $parameters Should be an empty array by default
     * @param array $typoScript The TypoScript configuration
     */
    protected function parseAdditionalGetParameters(array &$parameters, array $typoScript)
    {
        foreach ($typoScript as $key => $value) {
            if (is_array($value)) {
                $key = rtrim($key, '.');
                $parameters[$key] = [];
                $this->parseAdditionalGetParameters($parameters[$key], $value);
            } else {
                $parameters[$key] = $value;
            }
        }
    }
}
