<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\MetaTag\Generator;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Resource\FileCollector;
use YoastSeoForTypo3\YoastSeo\Record\Record;

class OpenGraphGenerator extends AbstractGenerator
{
    public function generate(Record $record): void
    {
        $ogTitle = $record->getRecordData()['og_title'] ?? '';
        if (!empty($ogTitle)) {
            $manager = $this->managerRegistry->getManagerForProperty('og:title');
            $manager->removeProperty('og:title');
            $manager->addProperty('og:title', $ogTitle);
        }

        $ogDescription = $record->getRecordData()['og_description'] ?? '';
        if ($ogDescription) {
            $manager = $this->managerRegistry->getManagerForProperty('og:description');
            $manager->removeProperty('og:description');
            $manager->addProperty('og:description', $ogDescription);
        }

        if ($record->getRecordData()['og_image'] ?? false) {
            $fileCollector = GeneralUtility::makeInstance(FileCollector::class);
            $fileCollector->addFilesFromRelation($record->getTableName(), 'og_image', $record->getRecordData());
            $manager = $this->managerRegistry->getManagerForProperty('og:image');

            $ogImages = $this->generateSocialImages($fileCollector->getFiles());
            if (count($ogImages) > 0) {
                $manager->removeProperty('og:image');
            }
            foreach ($ogImages as $ogImage) {
                $subProperties = [];
                $subProperties['url'] = $ogImage['url'];
                $subProperties['width'] = $ogImage['width'];
                $subProperties['height'] = $ogImage['height'];

                if (!empty($ogImage['alternative'])) {
                    $subProperties['alt'] = $ogImage['alternative'];
                }

                $manager->addProperty(
                    'og:image',
                    $ogImage['url'],
                    $subProperties
                );
            }
        }
    }
}
