<?php
$llPrefix = 'LLL:EXT:yoast_seo/Resources/Private/Language/BackendModule.xlf:';

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns(
    'tt_content',
    [
        'tx_yoastseo_linking_suggestions' => [
            'label' => $llPrefix . 'tx_yoastseo_linking_suggestions',
            'exclude' => true,
            'config' => [
                'type' => 'text',
                'renderType' => 'internalLinkingSuggestion'
            ]
        ]
    ]
);
foreach ($GLOBALS['TCA']['tt_content']['types'] as $type => $config) {
    $GLOBALS['TCA']['tt_content']['types'][$type]['showitem'] =
        str_replace(
            'bodytext;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:bodytext_formlabel,',
            'bodytext;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:bodytext_formlabel,tx_yoastseo_linking_suggestions,',
            $GLOBALS['TCA']['tt_content']['types'][$type]['showitem']
        );

}
