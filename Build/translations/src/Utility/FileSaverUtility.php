<?php

/**
 * This file is part of the "yoast_seo" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace YoastSeoForTypo3\Translations\Utility;

class FileSaverUtility
{
    public static function saveToFile(string $filePath, string $content): void
    {
        file_put_contents($filePath, $content);
    }
}
