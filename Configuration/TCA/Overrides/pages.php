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
        'last_mod' => [
            'exclude' => 1,
            'label' => $llPrefix . 'last_mod',
            'config' => [
                'type' => 'input',
                'size' => '13',
                'eval' => 'datetime',
                'default' => '0',
                'readOnly' => true,
            ],
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
        'seo_title' => [
            'label' => $llPrefix . 'seoTitle',
            'exclude' => true,
            'config' => [
                'type' => 'input'
            ]
        ],
        'canonical_url' => [
            'label' => $llPrefix . 'canonical',
            'exclude' => true,
            'config' => [
                'type' => 'input',
                'size' => 50,
                'max' => 1024,
                'eval' => 'trim',
                'wizards' => [
                    'link' => [
                        'type' => 'popup',
                        'title' => $llPrefix . 'canonical',
                        'icon' => 'actions-wizard-link',
                        'module' => [
                            'name' => 'wizard_link',
                        ],
                        'params' => [
                            'blindLinkOptions' => 'file, folder, mail, spec',
                            'blindLinkFields' => '',
                        ],
                        'JSopenParams' => 'height=300,width=500,status=0,menubar=0,scrollbars=1'
                    ]
                ],
                'softref' => 'typolink'
            ]
        ],
        'no_index' => [
            'label' => $llPrefix . 'noIndex',
            'exclude' => true,
            'config' => [
                'type' => 'check'
            ]
        ],
        'no_follow' => [
            'label' => $llPrefix . 'noFollow',
            'exclude' => true,
            'config' => [
                'type' => 'check'
            ]
        ],
        'og_title' => [
            'label' => $llPrefix . 'og.title',
            'exclude' => true,
            'config' => [
                'type' => 'input'
            ]
        ],
        'og_description' => [
            'label' => $llPrefix . 'og.description',
            'exclude' => true,
            'config' => [
                'type' => 'input'
            ]
        ],
        'og_image' => [
            'label' => $llPrefix . 'og.image',
            'exclude' => true,
            'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig(
                'og_image',
                [
                    // Use the imageoverlayPalette instead of the basicoverlayPalette
                    'foreign_types' => [
                        '0' => [
                            'showitem' => '
                                --palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                                --palette--;;filePalette'
                        ],
                        \TYPO3\CMS\Core\Resource\File::FILETYPE_IMAGE => [
                            'showitem' => '
                                --palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                                --palette--;;filePalette'
                        ]
                    ]
                ],
                $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext']
            )
        ],
        'twitter_title' => [
            'label' => $llPrefix . 'twitter.title',
            'exclude' => true,
            'config' => [
                'max' => 70,
                'type' => 'input'
            ]
        ],
        'twitter_description' => [
            'label' => $llPrefix . 'twitter.description',
            'exclude' => true,
            'config' => [
                'max' => 200,
                'type' => 'input'
            ]
        ],
        'twitter_image' => [
            'label' => $llPrefix . 'twitter.image',
            'exclude' => true,
            'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig(
                'twitter_image',
                [
                    // Use the imageoverlayPalette instead of the basicoverlayPalette
                    'foreign_types' => [
                        '0' => [
                            'showitem' => '
                                --palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                                --palette--;;filePalette'
                        ],
                        \TYPO3\CMS\Core\Resource\File::FILETYPE_IMAGE => [
                            'showitem' => '
                                --palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                                --palette--;;filePalette'
                        ]
                    ]
                ],
                $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext']
            )
        ],
        'tx_yoastseo_cornerstone' => [
            'label' => '',
            'exclude' => true,
            'config' => [
                'type' => 'input',
                'default' => 0,
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
    'yoast-metadata',
    '
    --linebreak--, tx_yoastseo_snippetpreview, tx_yoastseo_score_readability, tx_yoastseo_score_seo,
    --linebreak--, seo_title,
    --linebreak--, description,
    --linebreak--, tx_yoastseo_cornerstone
    '
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
    'yoast-robot',
    '
    --linebreak--, no_index, no_follow
    '
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette(
    'pages',
    'yoast-advanced',
    '
    --linebreak--, canonical_url,
    --linebreak--, tx_yoastseo_hide_snippet_preview, tx_yoastseo_dont_use, last_mod
    '
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette(
    'pages',
    'yoast-social-og',
    '
    --linebreak--, og_title, 
    --linebreak--, og_description, 
    --linebreak--, og_image 
    '
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette(
    'pages',
    'yoast-social-twitter',
    '
    --linebreak--, twitter_title, 
    --linebreak--, twitter_description, 
    --linebreak--, twitter_image 
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
    --div--;' . $llPrefix . 'pages.tabs.seo,
        --palette--;' . $llPrefix . 'pages.palettes.metadata;yoast-metadata,
        --palette--;' . $llPrefix . 'pages.palettes.readability;yoast-readability,
        --palette--;' . $llPrefix . 'pages.palettes.seo;yoast-focuskeyword,
        --palette--;' . $llPrefix . 'pages.palettes.og;yoast-social-og,
        --palette--;' . $llPrefix . 'pages.palettes.twitter;yoast-social-twitter,
        --palette--;' . $llPrefix . 'pages.palettes.robot;yoast-robot,
        --palette--;' . $llPrefix . 'pages.palettes.advanced;yoast-advanced,
    ',
    $dokTypes,
    'after:subtitle'
);
