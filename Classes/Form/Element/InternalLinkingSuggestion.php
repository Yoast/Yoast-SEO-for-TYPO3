<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Form\Element;

use TYPO3\CMS\Backend\Form\AbstractNode;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use YoastSeoForTypo3\YoastSeo\Service\Form\NodeTemplateService;
use YoastSeoForTypo3\YoastSeo\Service\Javascript\JsonConfigService;
use YoastSeoForTypo3\YoastSeo\Service\LocaleService;
use YoastSeoForTypo3\YoastSeo\Utility\PathUtility;

class InternalLinkingSuggestion extends AbstractNode
{
    public function __construct(
        protected LocaleService $localeService,
        protected NodeTemplateService $templateService,
        protected JsonConfigService $jsonConfigService,
        protected UriBuilder $uriBuilder
    ) {}

    public function setData(array $data): void
    {
        $this->data = $data;
    }

    protected int $languageId;
    protected int $currentPage;

    /**
     * @return array<string, mixed>
     */
    public function render(): array
    {
        $this->init();

        $resultArray = $this->initializeResultArray();

        if (($locale = $this->localeService->getLocale($this->currentPage, $this->languageId)) === null) {
            $resultArray['html'] = $this->templateService->renderView(
                'InternalLinkingSuggestion',
                ['languageError' => true]
            );
            return $resultArray;
        }
        $resultArray['stylesheetFiles'][] = 'EXT:yoast_seo/Resources/Public/CSS/yoast.min.css';

        $this->jsonConfigService->addConfig([
            'isCornerstoneContent' => false,
            'focusKeyphrase' => [
                'keyword' => '',
                'synonyms' => '',
            ],
            'data' => [
                'languageId' => $this->languageId,
            ],
            'linkingSuggestions' => [
                'excludedPage' => $this->currentPage,
                'locale' => $locale,
            ],
            'urls' => [
                'workerUrl' => PathUtility::getPublicPathToResources() . '/JavaScript/dist/worker.js',
                'linkingSuggestions' => (string)$this->uriBuilder->buildUriFromRoute(
                    'ajax_yoast_internal_linking_suggestions'
                ),
            ],
            'translations' => [$this->localeService->getTranslations()],
            'supportedLanguages' => $this->localeService->getSupportedLanguages(),
        ]);

        $resultArray['html'] = $this->templateService->renderView('InternalLinkingSuggestion');

        return $resultArray;
    }

    protected function init(): void
    {
        $this->currentPage = $this->data['parentPageRow']['uid'];
        $this->languageId = $this->localeService->getLanguageIdFromData($this->data);
    }
}
