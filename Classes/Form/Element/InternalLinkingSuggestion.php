<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Form\Element;

use TYPO3\CMS\Backend\Form\AbstractNode;
use TYPO3\CMS\Backend\Form\NodeFactory;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Core\Exception\SiteNotFoundException;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;
use YoastSeoForTypo3\YoastSeo\Utility\JavascriptUtility;
use YoastSeoForTypo3\YoastSeo\Utility\JsonConfigUtility;
use YoastSeoForTypo3\YoastSeo\Utility\PathUtility;

class InternalLinkingSuggestion extends AbstractNode
{
    protected StandaloneView $templateView;
    protected int $languageId;
    protected int $currentPage;

    public function __construct(NodeFactory $nodeFactory, array $data)
    {
        parent::__construct($nodeFactory, $data);

        $this->currentPage = $data['parentPageRow']['uid'];

        if (isset($data['databaseRow']['sys_language_uid'])) {
            if (is_array($data['databaseRow']['sys_language_uid']) && count(
                    $data['databaseRow']['sys_language_uid']
                ) > 0) {
                $this->languageId = (int)current($data['databaseRow']['sys_language_uid']);
            } else {
                $this->languageId = (int)$data['databaseRow']['sys_language_uid'];
            }
        }

        $this->templateView = GeneralUtility::makeInstance(StandaloneView::class);
        $this->templateView->setTemplatePathAndFilename(
            GeneralUtility::getFileAbsFileName(
                'EXT:yoast_seo/Resources/Private/Templates/TCA/InternalLinkingSuggestion.html'
            )
        );
    }

    public function render(): array
    {
        $locale = $this->getLocale($this->currentPage);
        if ($locale === null) {
            $this->templateView->assign('languageError', true);
            $resultArray['html'] = $this->templateView->render();
            return $resultArray;
        }

        $publicResourcesPath = PathUtility::getPublicPathToResources();

        $resultArray = $this->initializeResultArray();
        $resultArray['stylesheetFiles'][] = 'EXT:yoast_seo/Resources/Public/CSS/yoast.min.css';
        $jsonConfigUtility = GeneralUtility::makeInstance(JsonConfigUtility::class);

        $workerUrl = $publicResourcesPath . '/JavaScript/dist/worker.js';

        $config = [
            'isCornerstoneContent' => false,
            'focusKeyphrase' => [
                'keyword' => '',
                'synonyms' => ''
            ],
            'data' => [
                'languageId' => $this->languageId
            ],
            'linkingSuggestions' => [
                'excludedPage' => $this->currentPage,
                'locale' => $locale
            ],
            'urls' => [
                'workerUrl' => $workerUrl,
                'linkingSuggestions' => (string)GeneralUtility::makeInstance(UriBuilder::class)
                    ->buildUriFromRoute('ajax_yoast_internal_linking_suggestions')
            ]
        ];
        $jsonConfigUtility->addConfig($config);

        $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
        $pageRenderer->addRequireJsConfiguration([
            'paths' => [
                'YoastSEO' => $publicResourcesPath . '/JavaScript/',
            ]
        ]);
        JavascriptUtility::loadJavascript($pageRenderer);

        $resultArray['html'] = $this->templateView->render();

        return $resultArray;
    }

    protected function getLocale(int $pageId): ?string
    {
        $siteFinder = GeneralUtility::makeInstance(SiteFinder::class);
        try {
            $site = $siteFinder->getSiteByPageId($pageId);
            if ($this->languageId === -1) {
                $this->languageId = $site->getDefaultLanguage()->getLanguageId();
                return $site->getDefaultLanguage()->getTwoLetterIsoCode();
            }
            return $site->getLanguageById($this->languageId)->getTwoLetterIsoCode();
        } catch (SiteNotFoundException|\InvalidArgumentException $e) {
            return null;
        }
    }
}
