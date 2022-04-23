<?php
declare(strict_types=1);
namespace YoastSeoForTypo3\YoastSeo\Utility;

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

class PathUtility
{
    public static function getPublicPathToResources(): string
    {
        $isNewComposerMode = ComposerUtility::isNewComposerMode();
        if($isNewComposerMode === true) {
            return '/_assets/083b2b9e55c480021a1a9e04529d44d1';
        }
        $publicResourcesPath =
            \TYPO3\CMS\Core\Utility\PathUtility::getAbsoluteWebPath(ExtensionManagementUtility::extPath('yoast_seo')) . 'Resources/Public';
        return $publicResourcesPath;
    }
}
