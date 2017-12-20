<?php
$llPrefix = 'LLL:EXT:yoast_seo/Resources/Private/Language/BackendModule.xlf:';

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns(
    'pages_language_overlay',
    [
        'tx_yoastseo_title' => [
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
        'tx_yoastseo_canonical_url' => [
            'label' => $llPrefix . 'canonical',
            'exclude' => true,
            'config' => [
                'type' => 'input'
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
    'metatags',
    '
    --linebreak--, tx_yoastseo_title, tx_yoastseo_focuskeyword, tx_yoastseo_canonical_url, tx_yoastseo_robot_instructions,
    '
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette(
    'pages_language_overlay',
    'social-facebook',
    '
    --linebreak--, tx_yoastseo_facebook_title, 
    --linebreak--, tx_yoastseo_facebook_description, 
    --linebreak--, tx_yoastseo_facebook_image 
    '
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette(
    'pages_language_overlay',
    'social-twitter',
    '
    --linebreak--, tx_yoastseo_twitter_title, 
    --linebreak--, tx_yoastseo_twitter_description, 
    --linebreak--, tx_yoastseo_twitter_image 
    '
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
    'pages_language_overlay',
    '
        --palette--;' . $llPrefix . 'facebook;social-facebook,
        --palette--;' . $llPrefix . 'twitter;social-twitter,
    ',
    '',
    'after:tx_yoastseo_robot_instructions'
);
