<?php
$llPrefix = 'LLL:EXT:yoast_seo/Resources/Private/Language/BackendModule.xlf:';

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns(
    'pages_language_overlay',
    [
        'tx_yoastseo_snippetpreview' => [
            'label' => $llPrefix . 'snippetPreview',
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
            'config' => [
                'type' => 'text',
                'renderType' => 'readabilityAnalysis'
            ]
        ],
        'seo_title' => [
            'label' => $llPrefix . 'seoTitle',
            'config' => [
                'type' => 'input'
            ]
        ],
        'tx_yoastseo_focuskeyword' => [
            'label' => $llPrefix . 'seoFocusKeyword',
            'exclude' => true,
            'config' => [
                'type' => 'input'
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
        'tx_yoastseo_robot_instructions' => [
            'label' => $llPrefix . 'robotInstructions',
            'exclude' => true,
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['index, follow', 0],
                    ['noindex, nofollow', 1],
                    ['noindex, follow', 2],
                    ['index, nofollow', 3],
                ]
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
                        ],
                    ]
                ],
                $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext']
            )
        ],
        'tx_yoastseo_twitter_title' => [
            'label' => $llPrefix . 'twitter.title',
            'exclude' => true,
            'config' => [
                'type' => 'input'
            ]
        ],
        'tx_yoastseo_twitter_description' => [
            'label' => $llPrefix . 'twitter.description',
            'exclude' => true,
            'config' => [
                'type' => 'input'
            ]
        ],
        'tx_yoastseo_twitter_image' => [
            'label' => $llPrefix . 'twitter.image',
            'exclude' => true,
            'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig(
                'tx_yoastseo_twitter_image',
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
                        ],
                    ]
                ],
                $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext']
            )
        ]
    ]
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette(
    'pages_language_overlay',
    'yoast-metadata',
    '
    --linebreak--, tx_yoastseo_snippetpreview,
    --linebreak--, seo_title,
    --linebreak--, description,
    '
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette(
    'pages_language_overlay',
    'yoast-readability',
    '
    --linebreak--, tx_yoastseo_readability_analysis
    '
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette(
    'pages_language_overlay',
    'yoast-focuskeyword',
    '
    --linebreak--, tx_yoastseo_focuskeyword,
    --linebreak--, tx_yoastseo_focuskeyword_analysis
    '
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette(
    'pages_language_overlay',
    'yoast-robot',
    '
    --linebreak--, tx_yoastseo_robot_instructions
    '
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette(
    'pages_language_overlay',
    'yoast-advanced',
    '
    --linebreak--, canonical_url,
    '
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette(
    'pages_language_overlay',
    'yoast-social-og',
    '
    --linebreak--, og_title, 
    --linebreak--, og_description, 
    --linebreak--, tx_yoastseo_facebook_image 
    '
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette(
    'pages_language_overlay',
    'yoast-social-twitter',
    '
    --linebreak--, tx_yoastseo_twitter_title, 
    --linebreak--, tx_yoastseo_twitter_description, 
    --linebreak--, tx_yoastseo_twitter_image 
    '
);

$GLOBALS['TCA']['pages_language_overlay']['palettes']['metatags']['showitem'] =
    preg_replace('/description(.*,|.*$)/', '', $GLOBALS['TCA']['pages_language_overlay']['palettes']['metatags']['showitem']);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
    'pages_language_overlay',
    '
    --div--;' . $llPrefix . 'pages.tabs.seo,
        --palette--;' . $llPrefix . 'pages.palettes.metadata;yoast-metadata,
        --palette--;' . $llPrefix . 'pages.palettes.focuskeyword;yoast-focuskeyword,
        --palette--;' . $llPrefix . 'pages.palettes.readability;yoast-readability,
        --palette--;' . $llPrefix . 'pages.palettes.og;yoast-social-og,
        --palette--;' . $llPrefix . 'pages.palettes.twitter;yoast-social-twitter,
        --palette--;' . $llPrefix . 'pages.palettes.robot;yoast-robot,
        --palette--;' . $llPrefix . 'pages.palettes.advanced;yoast-advanced,
    ',
    '',
    'after:subtitle'
);
