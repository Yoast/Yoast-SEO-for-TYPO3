<?php

use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

defined('TYPO3') || die();

ExtensionManagementUtility::addTypoScriptConstants(
    "@import 'EXT:sitepackage/Configuration/TypoScript/constants.typoscript'"
);

ExtensionManagementUtility::addTypoScriptSetup(
    "@import 'EXT:sitepackage/Configuration/TypoScript/setup.typoscript'"
);

if ((new Typo3Version())->getMajorVersion() < 12) {
    ExtensionManagementUtility::addPageTSConfig(
        "@import 'EXT:sitepackage/Configuration/page.tsconfig'"
    );
}
