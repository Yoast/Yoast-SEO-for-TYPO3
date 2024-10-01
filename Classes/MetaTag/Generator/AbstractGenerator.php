<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\MetaTag\Generator;

use TYPO3\CMS\Core\Imaging\ImageManipulation\CropVariantCollection;
use TYPO3\CMS\Core\MetaTag\MetaTagManagerRegistry;
use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\Resource\ProcessedFile;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Service\ImageService;

abstract class AbstractGenerator implements GeneratorInterface
{
    public function __construct(
        protected MetaTagManagerRegistry $managerRegistry
    ) {}

    /**
     * @see \TYPO3\CMS\Seo\MetaTag\MetaTagGenerator
     * @param FileReference[] $fileReferences
     * @return array<array{url: string, width: float, height: float, alternative: string}>
     */
    protected function generateSocialImages(array $fileReferences): array
    {
        $imageService = GeneralUtility::makeInstance(ImageService::class);

        $socialImages = [];

        foreach ($fileReferences as $fileReference) {
            $arguments = $fileReference->getProperties();
            $image = $this->processSocialImage($fileReference);
            $socialImages[] = [
                'url' => $imageService->getImageUri($image, true),
                'width' => floor((float)$image->getProperty('width')),
                'height' => floor((float)$image->getProperty('height')),
                'alternative' => $arguments['alternative'],
            ];
        }

        return $socialImages;
    }

    protected function processSocialImage(FileReference $fileReference): FileInterface
    {
        $arguments = $fileReference->getProperties();
        $cropVariantCollection = CropVariantCollection::create((string)($arguments['crop'] ?? ''));
        $cropVariantName = ($arguments['cropVariant'] ?? false) ?: 'social';
        $cropArea = $cropVariantCollection->getCropArea($cropVariantName);
        $crop = $cropArea->makeAbsoluteBasedOnFile($fileReference);

        $processingConfiguration = [
            'crop' => $crop,
            'maxWidth' => 2000,
        ];

        // The image needs to be processed if:
        //  - the image width is greater than the defined maximum width, or
        //  - there is a cropping other than the full image (starts at 0,0 and has a width and height of 100%) defined
        $needsProcessing = $fileReference->getProperty('width') > $processingConfiguration['maxWidth']
            || !$cropArea->isEmpty();
        if (!$needsProcessing) {
            return $fileReference->getOriginalFile();
        }

        return $fileReference->getOriginalFile()->process(
            ProcessedFile::CONTEXT_IMAGECROPSCALEMASK,
            $processingConfiguration
        );
    }
}
