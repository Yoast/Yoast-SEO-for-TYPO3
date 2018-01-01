<?php
$llPrefix = 'LLL:EXT:yoast_seo/Resources/Private/Language/BackendModule.xlf:';

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns(
    'pages',
    [
        'tx_yoastseo_snippetpreview' => [
            'label' => $llPrefix . 'snippetPreview',
            'exclude' => true,
            'config' => [
                'type' => 'text',
                'renderType' => 'snippetPreview',
                'settings' => [
                    'titleField' => 'tx_yoastseo_title',
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
        'tx_yoastseo_dont_use' => [
            'label' => $llPrefix . 'hideYoastInFrontend',
            'exclude' => true,
            'config' => [
                'type' => 'check'
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
        'tx_yoastseo_title' => [
            'label' => $llPrefix . 'seoTitle',
            'exclude' => true,
            'config' => [
                'type' => 'input'
            ]
        ],
        'tx_yoastseo_canonical_url' => [
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
                        'title' => $llPrefix.'canonical',
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
        'tx_yoastseo_facebook_title' => [
            'label' => $llPrefix . 'facebook.title',
            'exclude' => true,
            'config' => [
                'type' => 'input'
            ]
        ],
        'tx_yoastseo_facebook_description' => [
            'label' => $llPrefix . 'facebook.description',
            'exclude' => true,
            'config' => [
                'type' => 'input'
            ]
        ],
        'tx_yoastseo_facebook_image' => [
            'label' => $llPrefix . 'facebook.image',
            'exclude' => true,
            'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig(
                'tx_yoastseo_facebook_image',
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
        'tx_yoastseo_twitter_title' => [
            'label' => $llPrefix . 'twitter.title',
            'exclude' => true,
            'config' => [
                'max' => 70,
                'type' => 'input'
            ]
        ],
        'tx_yoastseo_twitter_description' => [
            'label' => $llPrefix . 'twitter.description',
            'exclude' => true,
            'config' => [
                'max' => 200,
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
                        ]
                    ]
                ],
                $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext']
            )
        ],
    ]
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette(
    'pages',
    'yoast-metadata',
    '
    --linebreak--, tx_yoastseo_snippetpreview,
    --linebreak--, tx_yoastseo_title,
    --linebreak--, description,
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
    --linebreak--, tx_yoastseo_robot_instructions
    '
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette(
    'pages',
    'yoast-advanced',
    '
    --linebreak--, tx_yoastseo_canonical_url,
    --linebreak--, tx_yoastseo_dont_use
    '
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette(
    'pages',
    'yoast-social-og',
    '
    --linebreak--, tx_yoastseo_facebook_title, 
    --linebreak--, tx_yoastseo_facebook_description, 
    --linebreak--, tx_yoastseo_facebook_image 
    '
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette(
    'pages',
    'yoast-social-twitter',
    '
    --linebreak--, tx_yoastseo_twitter_title, 
    --linebreak--, tx_yoastseo_twitter_description, 
    --linebreak--, tx_yoastseo_twitter_image 
    '
);

$GLOBALS['TCA']['pages']['palettes']['metatags']['showitem'] =
    preg_replace('/description(.*,|.*$)/', '', $GLOBALS['TCA']['pages']['palettes']['metatags']['showitem']);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
    'pages',
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
