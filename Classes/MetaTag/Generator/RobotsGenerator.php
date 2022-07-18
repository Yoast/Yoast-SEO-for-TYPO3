<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\MetaTag\Generator;

use YoastSeoForTypo3\YoastSeo\Record\Record;

class RobotsGenerator extends AbstractGenerator
{
    public function generate(Record $record): void
    {
        if (!isset($record->getRecordData()['no_index'], $record->getRecordData()['no_follow'])) {
            return;
        }

        $noIndex = ((bool)$record->getRecordData()['no_index']) ? 'noindex' : 'index';
        $noFollow = ((bool)$record->getRecordData()['no_follow']) ? 'nofollow' : 'follow';

        if ($noIndex === 'noindex' || $noFollow === 'nofollow') {
            $manager = $this->managerRegistry->getManagerForProperty('robots');
            $manager->removeProperty('robots');
            $manager->addProperty('robots', implode(',', [$noIndex, $noFollow]));
        }
    }
}
