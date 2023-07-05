<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\MetaTag\Generator;

use YoastSeoForTypo3\YoastSeo\Record\Record;

class DescriptionGenerator extends AbstractGenerator
{
    public function generate(Record $record): void
    {
        $description = $record->getRecordData()[$record->getDescriptionField()] ?? '';

        if (!empty($description)) {
            $manager = $this->managerRegistry->getManagerForProperty('description');
            $manager->removeProperty('description');
            $manager->addProperty('description', $description);
        }
    }
}
