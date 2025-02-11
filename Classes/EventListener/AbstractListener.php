<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\EventListener;

use YoastSeoForTypo3\YoastSeo\Record\Builder\AbstractBuilder;
use YoastSeoForTypo3\YoastSeo\Record\Record;
use YoastSeoForTypo3\YoastSeo\Record\RecordRegistry;

abstract class AbstractListener
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
