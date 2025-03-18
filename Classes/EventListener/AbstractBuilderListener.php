<?php

/**
 * This file is part of the "yoast_seo" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\EventListener;

use YoastSeoForTypo3\YoastSeo\Record\Builder\AbstractBuilder;
use YoastSeoForTypo3\YoastSeo\Record\Record;
use YoastSeoForTypo3\YoastSeo\Record\RecordRegistry;

abstract class AbstractBuilderListener
{
    public function __construct(
        protected AbstractBuilder $builder
    ) {}

    /**
     * @return Record[]
     */
    protected function getRecordsFromRegistry(): array
    {
        return RecordRegistry::getInstance()->getRecords();
    }
}
