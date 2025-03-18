<?php

/**
 * This file is part of the "yoast_seo" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Form\Element;

use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use TYPO3\CMS\Backend\Form\AbstractNode;
use TYPO3\CMS\Core\Page\JavaScriptModuleInstruction;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\Resource\FileRepository;
use TYPO3\CMS\Core\Site\Entity\Site;
use YoastSeoForTypo3\YoastSeo\Service\Form\NodeTemplateService;

#[Autoconfigure(public: true)]
abstract class AbstractSocialPreview extends AbstractNode
{
    public function __construct(
        protected NodeTemplateService $templateService,
        protected FileRepository $fileRepository,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public function setData(array $data): void
    {
        $this->data = $data;
    }

    /**
     * @return array<string, mixed>
     */
    public function render(): array
    {
        $resultArray = $this->initializeResultArray();

        $resultArray['javaScriptModules'][] = JavaScriptModuleInstruction::create(
            '@yoast/yoast-seo-for-typo3/' . $this->getSocialType() . '-preview.js'
        )->invoke('initialize', ['fieldSelectors' => $this->formatFieldSelectors()]);

        $resultArray['html'] = $this->templateService->renderView(ucfirst($this->getSocialType()) . 'Preview', [
            'data' => $this->data,
            'socialType' => $this->getSocialType(),
            'siteBase' => $this->getSiteBase(),
            'image' => $this->getImage(),
        ]);
        return $resultArray;
    }

    /**
     * @return array<string, string>
     */
    protected function formatFieldSelectors(): array
    {
        $fieldSelectors = $this->getFieldSelectors();
        array_walk($fieldSelectors, function (&$value, $key) {
            if (str_contains($key, 'ImageContainer')) {
                return;
            }
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
        $fileReferences = $this->fileRepository->findByRelation($this->data['tableName'], $this->getImageField(), $this->data['vanillaUid']);
        if (isset($fileReferences[0]) && $fileReferences[0] instanceof FileReference) {
            return $fileReferences[0];
        }
        return null;
    }

    protected function getImageContainerSelector(string $imageField): string
    {
        return 'data-' . $this->data['effectivePid'] . '-' . $this->data['tableName'] . '-' . $this->data['vanillaUid'] . '-' . $imageField;
    }

    abstract protected function getSocialType(): string;

    /**
     * @return array<string, string>
     */
    abstract protected function getFieldSelectors(): array;
    abstract protected function getImageField(): string;
}
