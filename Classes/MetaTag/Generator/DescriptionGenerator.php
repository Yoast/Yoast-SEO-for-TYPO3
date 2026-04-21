<?php

/**
 * This file is part of the "yoast_seo" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

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
