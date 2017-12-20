<?php
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns(
    'pages',
    [
        'tx_yoastseo_dont_use' => [
            'label' => 'Hide Yoast SEO in frontend',
            'exclude' => true,
            'config' => [
                'type' => 'check'
            ]
        ],
        'tx_yoastseo_focuskeyword' => [
            'label' => 'SEO focus keyword',
            'exclude' => true,
            'config' => [
                'type' => 'input'
            ]
        ],
        'tx_yoastseo_title' => [
            'label' => 'SEO title',
            'config' => [
                'type' => 'input'
            ]
        ],
        'tx_yoastseo_canonical_url' => [
            'label' => 'Canonical URL',
            'exclude' => true,
            'config' => [
                'type' => 'input'
            ]
        ],
        'tx_yoastseo_robot_instructions' => [
            'label' => 'Robot instructions',
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
            'label' => 'Facebook title',
            'exclude' => true,
            'config' => [
                'type' => 'input'
            ]
        ],
        'tx_yoastseo_facebook_description' => [
            'label' => 'Facebook description',
            'exclude' => true,
            'config' => [
                'type' => 'input'
            ]
        ],
        'tx_yoastseo_facebook_image' => [
            'label' => 'Facebook image',
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
            'label' => 'Twitter title',
            'exclude' => true,
            'config' => [
                'type' => 'input'
            ]
        ],
        'tx_yoastseo_twitter_description' => [
            'label' => 'Twitter description',
            'exclude' => true,
            'config' => [
                'type' => 'input'
            ]
        ],
        'tx_yoastseo_twitter_image' => [
            'label' => 'Twitter image',
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
    'metatags',
    '
    --linebreak--, tx_yoastseo_title, tx_yoastseo_focuskeyword, tx_yoastseo_canonical_url, tx_yoastseo_robot_instructions,
    --linebreak--, tx_yoastseo_facebook_title, 
    --linebreak--, tx_yoastseo_facebook_description, 
    --linebreak--, tx_yoastseo_facebook_image, 
    --linebreak--, tx_yoastseo_twitter_title, 
    --linebreak--, tx_yoastseo_twitter_description, 
    --linebreak--, tx_yoastseo_twitter_image,
    --linebreak--, tx_yoastseo_dont_use
    '
);
