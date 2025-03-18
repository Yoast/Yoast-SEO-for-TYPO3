<?php

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use YoastSeoForTypo3\YoastSeo\Hooks\BackendYoastConfig;

defined('TYPO3') || die;

// Extend user settings
$GLOBALS['TYPO3_USER_SETTINGS']['columns']['hideYoastInPageModule'] = [
    'label' => 'LLL:EXT:yoast_seo/Resources/Private/Language/BackendModule.xlf:usersettings.hideYoastInPageModule',
    'type' => 'check',
];
ExtensionManagementUtility::addFieldsToUserSettings(
    '--div--;LLL:EXT:yoast_seo/Resources/Private/Language/BackendModule.xlf:usersettings.title,hideYoastInPageModule'
);

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_pagerenderer.php']['render-preProcess'][] = BackendYoastConfig::class . '->renderConfig';
