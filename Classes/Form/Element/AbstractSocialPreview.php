<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Form\Element;

use TYPO3\CMS\Backend\Form\AbstractNode;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\Resource\FileRepository;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use YoastSeoForTypo3\YoastSeo\Service\Form\NodeTemplateService;
use YoastSeoForTypo3\YoastSeo\Service\Javascript\JavascriptService;
use YoastSeoForTypo3\YoastSeo\Service\Javascript\JsonConfigService;

abstract class AbstractSocialPreview extends AbstractNode
{
    // TODO: Use constructor DI when TYPO3 v11 can be dropped
    protected NodeTemplateService $templateService;
    protected JavascriptService $javascriptService;
    protected JsonConfigService $jsonConfigService;

    public function render(): array
    {
        $this->initialize();

        $resultArray = $this->initializeResultArray();

        $this->javascriptService->loadPluginJavascript();
        $this->jsonConfigService->addConfig([
            'fieldSelectors' => $this->formatFieldSelectors(),
        ]);

        $resultArray['html'] = $this->templateService->renderView('SocialPreview', [
            'data' => $this->data,
            'socialType' => $this->getSocialType(),
            'siteBase' => $this->getSiteBase(),
            'image' => $this->getImage(),
        ]);
        return $resultArray;
    }

    protected function formatFieldSelectors(): array
    {
        $fieldSelectors = $this->getFieldSelectors();
        array_walk($fieldSelectors, function (&$value) {
            $value = 'data' . str_replace($this->data['fieldName'], $value, $this->data['elementBaseName']);
        });
        return $fieldSelectors;
    }

    protected function getSiteBase(): string
    {
        $languageId = 0;
        $languageField = $GLOBALS['TCA'][$this->data['tableName']]['ctrl']['languageField'] ?? '';
        if (isset($this->data['databaseRow'][$languageField]) && !empty($this->data['databaseRow'][$languageField])) {
            $languageId = (int)$this->data['databaseRow'][$languageField] > -1
                ? (int)$this->data['databaseRow'][$languageField]
                : 0;
        }

        /** @var Site $site */
        $site = $this->data['site'];
        $host = $site->getLanguageById($languageId)->getBase()->getHost();
        if (!empty($host)) {
            return $host;
        }

        // Fall back to request host
        return $this->data['request']->getUri()->getHost();
    }

    protected function getImage(): ?FileReference
    {
        $fileRepository = GeneralUtility::makeInstance(FileRepository::class);
        $fileReferences = $fileRepository->findByRelation($this->data['tableName'], $this->getImageField(), $this->data['vanillaUid']);
        if (isset($fileReferences[0]) && $fileReferences[0] instanceof FileReference) {
            return $fileReferences[0];
        }
        return null;
    }

    protected function initialize(): void
    {
        $this->templateService = GeneralUtility::makeInstance(NodeTemplateService::class);
        $this->javascriptService = GeneralUtility::makeInstance(JavascriptService::class);
        $this->jsonConfigService = GeneralUtility::makeInstance(JsonConfigService::class);
    }

    abstract protected function getSocialType(): string;
    abstract protected function getFieldSelectors(): array;
    abstract protected function getImageField(): string;
}
