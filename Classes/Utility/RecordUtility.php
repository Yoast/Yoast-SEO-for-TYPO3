<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Utility;

use YoastSeoForTypo3\YoastSeo\Record\Record;
use YoastSeoForTypo3\YoastSeo\Record\RecordRegistry;

class RecordUtility
{
    public static function configureForRecord(string $tableName): Record
    {
        return RecordRegistry::getInstance()->addRecord($tableName);
    }

    public static function removeForRecord(string $tableName): void
    {
        RecordRegistry::getInstance()->removeRecord($tableName);
    }
}
