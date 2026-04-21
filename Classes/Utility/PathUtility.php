<?php

/**
 * This file is part of the "yoast_seo" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Utility;

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\PathUtility as TYPO3PathUtility;

class PathUtility
{
    public static function getPublicPathToResources(): string
    {
        return TYPO3PathUtility::getAbsoluteWebPath(
            ExtensionManagementUtility::extPath('yoast_seo') . 'Resources/Public'
        );
    }
}
