<?php

/**
 * This file is part of the "yoast_seo" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Form\Element;

use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Core\Page\JavaScriptModuleInstruction;
use YoastSeoForTypo3\YoastSeo\Service\Form\NodeTemplateService;
use YoastSeoForTypo3\YoastSeo\Service\Javascript\JsonTranslationsService;
use YoastSeoForTypo3\YoastSeo\Service\LocaleService;
use YoastSeoForTypo3\YoastSeo\Utility\PathUtility;

class InternalLinkingSuggestion extends AbstractLabeledFormElement
{
    public function __construct(
        protected LocaleService $localeService,
        protected NodeTemplateService $templateService,
        protected JsonTranslationsService $jsonTranslationsService,
        protected UriBuilder $uriBuilder
    ) {}

    /**
     * @param array<string, mixed> $data
     */
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
            $resultArray['html'] = $this->getLabel() . $this->templateService->renderView(
                'InternalLinkingSuggestion',
                ['languageError' => true]
            );
            return $resultArray;
        }
        $resultArray['stylesheetFiles'][] = 'EXT:yoast_seo/Resources/Public/CSS/yoast.min.css';

        $resultArray['javaScriptModules'][] = JavaScriptModuleInstruction::create('@yoast/yoast-seo-for-typo3/linking-suggestions.js')->invoke(
            'initialize',
            [
                'urls' => [
                    'workerUrl' => PathUtility::getPublicPathToResources() . '/JavaScript/dist/worker.js',
                    'linkingSuggestions' => (string)$this->uriBuilder->buildUriFromRoute(
                        'ajax_yoast_internal_linking_suggestions'
                    ),
                ],
                'locale' => $locale,
                'languageId' => $this->languageId,
                'currentPage' => $this->currentPage,
                'supportedLanguages' => $this->localeService->getSupportedLanguages(),
            ]
        );

        $resultArray['html'] = $this->getLabel() . $this->templateService->renderView('InternalLinkingSuggestion');

        $this->jsonTranslationsService->addTranslations();

        return $resultArray;
    }

    protected function init(): void
    {
        $this->currentPage = $this->data['parentPageRow']['uid'];
        $this->languageId = $this->localeService->getLanguageIdFromData($this->data);
    }
}
