<?php
declare(strict_types=1);
namespace YoastSeoForTypo3\YoastSeo\Utility;

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\PathUtility as TYPO3PathUtility;

class PathUtility
{
    public static function getPublicPathToResources(): string
    {
        return TYPO3PathUtility::getAbsoluteWebPath(ExtensionManagementUtility::extPath('yoast_seo') . 'Resources/Public/');
    }
}
