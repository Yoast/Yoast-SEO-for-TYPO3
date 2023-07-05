<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\MetaTag\Generator;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Resource\FileCollector;
use YoastSeoForTypo3\YoastSeo\Record\Record;

class TwitterGenerator extends AbstractGenerator
{
    public function generate(Record $record): void
    {
        $twitterCard = $record->getRecordData()['twitter_card'] ?? '';
        if (!empty($twitterCard)) {
            $manager = $this->managerRegistry->getManagerForProperty('twitter:card');
            $manager->removeProperty('twitter:card');
            $manager->addProperty('twitter:card', $twitterCard);
        }

        $twitterTitle = $record->getRecordData()['twitter_title'] ?? '';
        if (!empty($twitterTitle)) {
            $manager = $this->managerRegistry->getManagerForProperty('twitter:title');
            $manager->removeProperty('twitter:title');
            $manager->addProperty('twitter:title', $twitterTitle);
        }

        $twitterDescription = $record->getRecordData()['twitter_description'];
        if (!empty($twitterDescription)) {
            $manager = $this->managerRegistry->getManagerForProperty('twitter:description');
            $manager->removeProperty('twitter:description');
            $manager->addProperty('twitter:description', $twitterDescription);
        }

        if ($record->getRecordData()['twitter_image']) {
            $fileCollector = GeneralUtility::makeInstance(FileCollector::class);
            $fileCollector->addFilesFromRelation($record->getTableName(), 'twitter_image', $record->getRecordData());
            $manager = $this->managerRegistry->getManagerForProperty('twitter:image');

            $twitterImages = $this->generateSocialImages($fileCollector->getFiles());
            if (count($twitterImages) > 0) {
                $manager->removeProperty('twitter:image');
            }
            foreach ($twitterImages as $twitterImage) {
                $subProperties = [];

                if (!empty($twitterImage['alternative'])) {
                    $subProperties['alt'] = $twitterImage['alternative'];
                }

                $manager->addProperty(
                    'twitter:image',
                    $twitterImage['url'],
                    $subProperties
                );
            }
        }
    }
}
