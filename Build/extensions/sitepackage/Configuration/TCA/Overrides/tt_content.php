<?php

/**
 * This file is part of the "yoast_seo" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

defined('TYPO3') || die;

ExtensionUtility::registerPlugin(
    'Sitepackage',
    'Minimal',
    'Minimal List and Detail',
    'yoast-icon',
);

$key = 'sitepackage_nestedsearchmarkers';

ExtensionManagementUtility::addTcaSelectItem(
    'tt_content',
    'CType',
    [
        'label' => 'Sitepackage - Content between search markers',
        'value' => $key,
        'group' => 'default',
    ],
    'textmedia',
    'after',
);

$GLOBALS['TCA']['tt_content']['types'][$key] = [
    'showitem' => '
            --palette--;;headers,
            bodytext,
        ',
];
