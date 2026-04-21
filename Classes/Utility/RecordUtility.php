<?php

/**
 * This file is part of the "yoast_seo" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

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
