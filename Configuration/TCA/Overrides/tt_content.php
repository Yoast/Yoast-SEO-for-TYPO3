<?php

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
                'renderType' => 'internalLinkingSuggestion'
            ]
        ]
    ]
);
foreach ($GLOBALS['TCA']['tt_content']['types'] as $type => $config) {
    if (!isset($GLOBALS['TCA']['tt_content']['types'][$type]['showitem'])) {
        continue;
    }
    $GLOBALS['TCA']['tt_content']['types'][$type]['showitem'] =
        str_replace(
            'bodytext;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:bodytext_formlabel,',
            'bodytext;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:bodytext_formlabel,tx_yoastseo_linking_suggestions,',
            $GLOBALS['TCA']['tt_content']['types'][$type]['showitem']
        );
}
