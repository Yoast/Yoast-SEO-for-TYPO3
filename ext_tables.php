<?php

/**
 * This file is part of the "yoast_seo" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use YoastSeoForTypo3\YoastSeo\Hooks\BackendYoastTranslations;

defined('TYPO3') || die;

// Extend user settings (see Configuration/Overrides/be_users.php for TYPO3 v14 and above)
if ((new Typo3Version())->getMajorVersion() < 14) {
    $GLOBALS['TYPO3_USER_SETTINGS']['columns']['hideYoastInPageModule'] = [
        'label' => 'LLL:EXT:yoast_seo/Resources/Private/Language/BackendModule.xlf:usersettings.hideYoastInPageModule',
        'type' => 'check',
    ];
    ExtensionManagementUtility::addFieldsToUserSettings(
        '--div--;LLL:EXT:yoast_seo/Resources/Private/Language/BackendModule.xlf:usersettings.title,hideYoastInPageModule'
    );
}

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_pagerenderer.php']['render-preProcess'][]
    = BackendYoastTranslations::class . '->renderTranslations';
