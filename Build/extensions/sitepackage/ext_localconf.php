<?php

/**
 * This file is part of the "yoast_seo" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;
use YoastSeoForTypo3\Sitepackage\Controller\RecordController;

defined('TYPO3') || die();

ExtensionManagementUtility::addTypoScriptConstants(
    "@import 'EXT:sitepackage/Configuration/TypoScript/constants.typoscript'"
);

ExtensionManagementUtility::addTypoScriptSetup(
    "@import 'EXT:sitepackage/Configuration/TypoScript/setup.typoscript'"
);

ExtensionUtility::configurePlugin(
    'Sitepackage',
    'Minimal',
    [RecordController::class => 'minimalList, minimalDetail'],
    [],
    ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
);

$GLOBALS['TYPO3_CONF_VARS']['SYS']['features']['yoastSeoInclusiveLanguage'] = true;
