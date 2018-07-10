<?php
if (!\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('cal')) {
    return;
}

$llPrefix = 'LLL:EXT:yoast_seo/Resources/Private/Language/BackendModule.xlf:';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns(
    'tx_cal_event',
    [
        'seo_title' => [
            'label' => $llPrefix . 'seoTitle',
            'exclude' => true,
            'config' => [
                'type' => 'input'
            ]
        ],
        'seo_description' => [
            'exclude' => true,
            'label' => $llPrefix . 'seoDescription',
            'config' => [
                'type' => 'text',
                'cols' => 30,
                'rows' => 5,
            ]
        ],
        'tx_yoastseo_snippetpreview' => [
            'label' => 'LLL:EXT:yoast_seo/Resources/Private/Language/BackendModule.xlf:snippetPreview',
            'exclude' => true,
            'displayCond' => 'REC:NEW:false',
            'config' => [
                'type' => 'text',
                'renderType' => 'snippetPreview',
                'settings' => [
                    'titleField' => 'seo_title',
                    'descriptionField' => 'seo_description'
                ]
            ]
        ],
        'tx_yoastseo_readability_analysis' => [
            'label' => $llPrefix . 'analysis',
            'exclude' => true,
            'config' => [
                'type' => 'text',
                'renderType' => 'readabilityAnalysis'
            ]
        ],
        'tx_yoastseo_focuskeyword' => [
            'label' => $llPrefix . 'seoFocusKeyword',
            'exclude' => true,
            'config' => [
                'type' => 'input',
            ]
        ],
        'tx_yoastseo_focuskeyword_analysis' => [
            'label' => $llPrefix . 'analysis',
            'exclude' => true,
            'config' => [
                'type' => 'input',
                'renderType' => 'focusKeywordAnalysis',
                'settings' => [
                    'focusKeywordField' => 'tx_yoastseo_focuskeyword',
                ]
            ]
        ],

    ]
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette(
    'tx_cal_event',
    'yoast-metadata',
    '
    --linebreak--, tx_yoastseo_snippetpreview,
    --linebreak--, seo_title,
    --linebreak--, seo_description,
    '
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette(
    'tx_cal_event',
    'yoast-focuskeyword',
    '
    --linebreak--, tx_yoastseo_focuskeyword,
    --linebreak--, tx_yoastseo_focuskeyword_analysis
    '
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette(
    'tx_cal_event',
    'yoast-readability',
    '
    --linebreak--, tx_yoastseo_readability_analysis
    '
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
    'tx_cal_event',
    '
    --div--;' . $llPrefix . 'pages.tabs.seo,
        --palette--;' . $llPrefix . 'pages.palettes.metadata;yoast-metadata,
        --palette--;' . $llPrefix . 'pages.palettes.readability;yoast-readability,
        --palette--;' . $llPrefix . 'pages.palettes.focuskeyword;yoast-focuskeyword,
    ',
    '',
    'after:description'
);
