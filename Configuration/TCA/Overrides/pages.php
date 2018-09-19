<?php
$llPrefix = 'LLL:EXT:yoast_seo/Resources/Private/Language/BackendModule.xlf:';

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns(
    'pages',
    [
        'tx_yoastseo_snippetpreview' => [
            'label' => $llPrefix . 'snippetPreview',
            'exclude' => true,
            'displayCond' => [
                'AND' => [
                    'FIELD:tx_yoastseo_dont_use:REQ:false',
                    'FIELD:tx_yoastseo_hide_snippet_preview:REQ:false'
                ]
            ],
            'config' => [
                'type' => 'text',
                'renderType' => 'snippetPreview',
                'settings' => [
                    'titleField' => 'seo_title',
                    'descriptionField' => 'description'
                ]
            ]
        ],
        'tx_yoastseo_readability_analysis' => [
            'label' => $llPrefix . 'analysis',
            'exclude' => true,
            'displayCond' => [
                'AND' => [
                    'FIELD:tx_yoastseo_dont_use:REQ:false',
                    'FIELD:tx_yoastseo_hide_snippet_preview:REQ:false'
                ]
            ],
            'config' => [
                'type' => 'text',
                'renderType' => 'readabilityAnalysis'
            ]
        ],
        'tx_yoastseo_dont_use' => [
            'label' => $llPrefix . 'hideYoastInFrontend',
            'exclude' => true,
            'config' => [
                'type' => 'check'
            ]
        ],
        'tx_yoastseo_hide_snippet_preview' => [
            'label' => $llPrefix . 'hideSnippetPreview',
            'exclude' => true,
            'config' => [
                'type' => 'check'
            ]
        ],
        'tx_yoastseo_focuskeyword' => [
            'label' => $llPrefix . 'seoFocusKeyword',
            'exclude' => true,
            'displayCond' => [
                'AND' => [
                    'FIELD:tx_yoastseo_dont_use:REQ:false',
                    'FIELD:tx_yoastseo_hide_snippet_preview:REQ:false'
                ]
            ],
            'config' => [
                'type' => 'input',
            ]
        ],
        'tx_yoastseo_focuskeyword_analysis' => [
            'label' => $llPrefix . 'analysis',
            'exclude' => true,
            'displayCond' => [
                'AND' => [
                    'FIELD:tx_yoastseo_dont_use:REQ:false',
                    'FIELD:tx_yoastseo_hide_snippet_preview:REQ:false'
                ]
            ],
            'config' => [
                'type' => 'input',
                'renderType' => 'focusKeywordAnalysis',
                'settings' => [
                    'focusKeywordField' => 'tx_yoastseo_focuskeyword',
                ]
            ]
        ],
        'tx_yoastseo_cornerstone' => [
            'label' => '',
            'exclude' => true,
            'config' => [
                'type' => 'text',
                'renderType' => 'cornerstone'
            ]
        ],
        'tx_yoastseo_score_readability' => [
            'label' => '',
            'exclude' => false,
            'config' => [
                'type' => 'passthrough'
            ]
        ],
        'tx_yoastseo_score_seo' => [
            'label' => '',
            'exclude' => false,
            'config' => [
                'type' => 'passthrough'
            ]
        ],
    ]
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette(
    'pages',
    'seo',
    '
    --linebreak--, tx_yoastseo_snippetpreview, tx_yoastseo_score_readability, tx_yoastseo_score_seo, --linebreak--
    ',
    'before: seo_title'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette(
    'pages',
    'seo',
    '
    --linebreak--, description, --linebreak--, tx_yoastseo_cornerstone
    ',
    'after: seo_title'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette(
    'pages',
    'yoast-readability',
    '
    --linebreak--, tx_yoastseo_readability_analysis
    '
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette(
    'pages',
    'yoast-focuskeyword',
    '
    --linebreak--, tx_yoastseo_focuskeyword,
    --linebreak--, tx_yoastseo_focuskeyword_analysis
    '
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette(
    'pages',
    'yoast-advanced',
    '
    --linebreak--, tx_yoastseo_hide_snippet_preview, tx_yoastseo_dont_use
    '
);

$GLOBALS['TCA']['pages']['palettes']['metatags']['showitem'] =
    preg_replace('/description(.*,|.*$)/', '', $GLOBALS['TCA']['pages']['palettes']['metatags']['showitem']);

$dokTypes = '1';
try {
    $dokTypes = \YoastSeoForTypo3\YoastSeo\Utility\YoastUtility::getAllowedDoktypes(null, true);
} catch (\Doctrine\DBAL\Exception\TableNotFoundException $e) {
    $dokTypes = '1,6';
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
    'pages',
    '
        --palette--;Label;yoast-snippetpreview,
    ',
    $dokTypes,
    'before:seo_title'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
    'pages',
    '
        --palette--;' . $llPrefix . 'pages.palettes.readability;yoast-readability,
        --palette--;' . $llPrefix . 'pages.palettes.seo;yoast-focuskeyword,
    ',
    $dokTypes,
    'after: tx_yoastseo_cornerstone'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
    'pages',
    '
        --palette--;' . $llPrefix . 'pages.palettes.advances;yoast-advanced,
    ',
    $dokTypes,
    'after: twitter_image'
);
