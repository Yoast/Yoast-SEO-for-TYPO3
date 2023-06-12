<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\MetaTag\Generator;

use TYPO3\CMS\Core\Imaging\ImageManipulation\CropVariantCollection;
use TYPO3\CMS\Core\MetaTag\MetaTagManagerRegistry;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\Resource\ProcessedFile;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Service\ImageService;
use YoastSeoForTypo3\YoastSeo\Record\Record;

abstract class AbstractGenerator
{
    protected MetaTagManagerRegistry $managerRegistry;

    public function __construct(MetaTagManagerRegistry $managerRegistry = null)
    {
        if ($managerRegistry === null) {
            $managerRegistry = GeneralUtility::makeInstance(MetaTagManagerRegistry::class);
        }
        $this->managerRegistry = $managerRegistry;
    }

    /**
     * @see \TYPO3\CMS\Seo\MetaTag\MetaTagGenerator
     * @param array $fileReferences
     * @return array
     */
    protected function generateSocialImages(array $fileReferences): array
    {
        $imageService = GeneralUtility::makeInstance(ImageService::class);

        $socialImages = [];

        /** @var FileReference $file */
        foreach ($fileReferences as $file) {
            $arguments = $file->getProperties();
            $cropVariantCollection = CropVariantCollection::create((string)$arguments['crop']);
            $cropVariant = ($arguments['cropVariant'] ?? false) ?: 'social';
            $cropArea = $cropVariantCollection->getCropArea($cropVariant);
            $crop = $cropArea->makeAbsoluteBasedOnFile($file);

            $processingConfiguration = [
                'crop' => $crop,
                'maxWidth' => 2000,
            ];

            $processedImage = $file->getOriginalFile()->process(
                ProcessedFile::CONTEXT_IMAGECROPSCALEMASK,
                $processingConfiguration
            );

            $imageUri = $imageService->getImageUri($processedImage, true);

            $socialImages[] = [
                'url' => $imageUri,
                'width' => floor($processedImage->getProperty('width')),
                'height' => floor($processedImage->getProperty('height')),
                'alternative' => $arguments['alternative'],
            ];
        }

        return $socialImages;
    }

    abstract public function generate(Record $record): void;
}
