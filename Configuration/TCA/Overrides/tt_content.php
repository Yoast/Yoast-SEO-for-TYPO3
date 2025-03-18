<?php

/**
 * This file is part of the "yoast_seo" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

$llPrefix = 'LLL:EXT:yoast_seo/Resources/Private/Language/BackendModule.xlf:';

ExtensionManagementUtility::addTCAcolumns(
    'tt_content',
    [
        'tx_yoastseo_linking_suggestions' => [
            'label' => $llPrefix . 'tx_yoastseo_linking_suggestions',
            'exclude' => true,
            'config' => [
                'type' => 'none',
                'renderType' => 'internalLinkingSuggestion',
            ],
        ],
    ]
);
foreach ($GLOBALS['TCA']['tt_content']['types'] as $type => $config) {
    if (!isset($GLOBALS['TCA']['tt_content']['types'][$type]['showitem'])) {
        continue;
    }
    // v12 and v13
    $GLOBALS['TCA']['tt_content']['types'][$type]['showitem'] =
        str_replace(
            'bodytext;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:bodytext_formlabel,',
            'bodytext;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:bodytext_formlabel,tx_yoastseo_linking_suggestions,',
            $GLOBALS['TCA']['tt_content']['types'][$type]['showitem']
        );
    // v14
    $GLOBALS['TCA']['tt_content']['types'][$type]['showitem'] =
        preg_replace(
            '/bodytext,\s*--div--;/m',
            'bodytext,tx_yoastseo_linking_suggestions,--div--;',
            $GLOBALS['TCA']['tt_content']['types'][$type]['showitem']
        );
}
