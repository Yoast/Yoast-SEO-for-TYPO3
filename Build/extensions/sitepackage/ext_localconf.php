<?php

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

defined('TYPO3') || die();

ExtensionManagementUtility::addTypoScriptConstants(
    "@import 'EXT:sitepackage/Configuration/TypoScript/constants.typoscript'"
);

ExtensionManagementUtility::addTypoScriptSetup(
    "@import 'EXT:sitepackage/Configuration/TypoScript/setup.typoscript'"
);
