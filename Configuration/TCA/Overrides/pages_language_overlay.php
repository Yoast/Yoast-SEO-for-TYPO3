<?php
$llPrefix = 'LLL:EXT:yoast_seo/Resources/Private/Language/BackendModule.xlf:';

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns(
    'pages_language_overlay',
    [
        'tx_yoastseo_snippetpreview' => [
            'label' => $llPrefix . 'snippetPreview',
            'exclude' => true,
            'displayCond' => 'FIELD:tx_yoastseo_hide_snippet_preview:REQ:false',
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
            'displayCond' => 'FIELD:tx_yoastseo_hide_snippet_preview:REQ:false',
            'config' => [
                'type' => 'text',
                'renderType' => 'readabilityAnalysis'
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
            'displayCond' => 'FIELD:tx_yoastseo_hide_snippet_preview:REQ:false',
            'config' => [
                'type' => 'input',
            ]
        ],
        'tx_yoastseo_focuskeyword_analysis' => [
            'label' => $llPrefix . 'analysis',
            'exclude' => true,
            'displayCond' => 'FIELD:tx_yoastseo_hide_snippet_preview:REQ:false',
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
                'type' => 'input',
                'default' => 0,
                'renderType' => 'cornerstone'
            ]
        ],
        'tx_yoastseo_score_readability' => [
            'label' => '',
            'exclude' => false,
            'config' => [
                'type' => 'input',
                'renderType' => 'hiddenField'
            ]
        ],
        'tx_yoastseo_score_seo' => [
            'label' => '',
            'exclude' => false,
            'config' => [
                'type' => 'input',
                'renderType' => 'hiddenField'
            ]
        ],
    ]
);

if (!\YoastSeoForTypo3\YoastSeo\Utility\YoastUtility::isPremiumInstalled()) {
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns(
        'pages_language_overlay',
        [
            'tx_yoastseo_focuskeyword_synonyms' => [
                'label' => $llPrefix . 'synonyms',
                'exclude' => false,
                'displayCond' => 'FIELD:tx_yoastseo_hide_snippet_preview:REQ:false',
                'config' => [
                    'type' => 'text',
                    'renderType' => 'synonyms',
                ]
            ],
            'tx_yoastseo_focuskeyword_premium' => [
                'label' => $llPrefix . 'seoRelatedKeywords',
                'exclude' => true,
                'config' => [
                    'type' => 'text',
                    'renderType' => 'relatedKeyphrases'
                ]
            ],
        ]
    );
}
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette(
    'pages_language_overlay',
    'seo',
    '
    --linebreak--, tx_yoastseo_snippetpreview, --linebreak--
    ',
    'before: seo_title'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette(
    'pages_language_overlay',
    'seo',
    '
    --linebreak--, description, --linebreak--, tx_yoastseo_cornerstone
    ',
    'after: seo_title'
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
    --linebreak--, tx_yoastseo_focuskeyword_synonyms,
    --linebreak--, tx_yoastseo_focuskeyword_analysis
    '
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette(
    'pages_language_overlay',
    'yoast-relatedkeywords',
    '
    --linebreak--, tx_yoastseo_focuskeyword_premium 
    '
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette(
    'pages_language_overlay',
    'yoast-advanced',
    '
    --linebreak--, tx_yoastseo_hide_snippet_preview
    '
);

$GLOBALS['TCA']['pages_language_overlay']['palettes']['metatags']['showitem'] =
    preg_replace('/description(.*,|.*$)/', '', $GLOBALS['TCA']['pages_language_overlay']['palettes']['metatags']['showitem']);

$dokTypes = '1';
try {
    $dokTypes = \YoastSeoForTypo3\YoastSeo\Utility\YoastUtility::getAllowedDoktypes(null, true);
} catch (\Doctrine\DBAL\Exception\TableNotFoundException $e) {
    $dokTypes = '1,6';
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
    'pages_language_overlay',
    '
        --palette--;Label;yoast-snippetpreview,
    ',
    $dokTypes,
    'before:seo_title'
);

if (version_compare(TYPO3_branch, '9.5', '<')) {
    $openGraphCropConfiguration = [
        'config' => [
            'cropVariants' => [
                'default' => [
                    'disabled' => true,
                ],
                'social' => [
                    'title' => $llPrefix . 'imwizard.crop_variant.social',
                    'coverAreas' => [],
                    'cropArea' => [
                        'x' => '0.0',
                        'y' => '0.0',
                        'width' => '1.0',
                        'height' => '1.0'
                    ],
                    'allowedAspectRatios' => [
                        '1.91:1' => [
                            'title' => $llPrefix . 'imwizard.ratio.191_1',
                            'value' => 1.91 / 1
                        ],
                        'NaN' => [
                            'title' => 'LLL:EXT:lang/Resources/Private/Language/locallang_wizards.xlf:imwizard.ratio.free',
                            'value' => 0.0
                        ],
                    ],
                    'selectedRatio' => '1.91:1',
                ],
            ],
        ],
    ];

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns(
        'pages_language_overlay',
        [
            'seo_title' => [
                'label' => $llPrefix . 'seoTitle',
                'exclude' => true,
                'config' => [
                    'type' => 'input',
                ]
            ],
            'no_index' => [
                'exclude' => true,
                'l10n_mode' => 'exclude',
                'label' => $llPrefix . 'pages.no_index_formlabel',
                'config' => [
                    'type' => 'check',
                ]
            ],
            'no_follow' => [
                'exclude' => true,
                'l10n_mode' => 'exclude',
                'label' => $llPrefix . 'pages.no_follow_formlabel',
                'config' => [
                    'type' => 'check',
                ]
            ],
            'og_title' => [
                'exclude' => true,
                'l10n_mode' => 'prefixLangTitle',
                'label' => $llPrefix . 'og_title',
                'config' => [
                    'type' => 'input',
                    'size' => 40,
                    'max' => 255,
                    'eval' => 'trim'
                ]
            ],
            'og_description' => [
                'exclude' => true,
                'l10n_mode' => 'prefixLangTitle',
                'label' => $llPrefix . 'og_description',
                'config' => [
                    'type' => 'text',
                    'cols' => 40,
                    'rows' => 3
                ]
            ],
            'og_image' => [
                'exclude' => true,
                'label' => $llPrefix . 'og_image',
                'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig(
                    'og_image',
                    [
                        // Use the imageoverlayPalette instead of the basicoverlayPalette
                        'overrideChildTca' => [
                            'types' => [
                                '0' => [
                                    'showitem' => '
                                    --palette--;;imageoverlayPalette,
                                    --palette--;;filePalette'
                                ],
                                \TYPO3\CMS\Core\Resource\File::FILETYPE_IMAGE => [
                                    'showitem' => '
                                    --palette--;;imageoverlayPalette,
                                    --palette--;;filePalette'
                                ]
                            ],
                            'columns' => [
                                'crop' => $openGraphCropConfiguration
                            ]
                        ],
                        'behaviour' => [
                            'allowLanguageSynchronization' => true
                        ]
                    ],
                    $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext']
                )
            ],
            'twitter_title' => [
                'exclude' => true,
                'l10n_mode' => 'prefixLangTitle',
                'label' => $llPrefix . 'twitter_title',
                'config' => [
                    'type' => 'input',
                    'size' => 40,
                    'max' => 255,
                    'eval' => 'trim'
                ]
            ],
            'twitter_description' => [
                'exclude' => true,
                'l10n_mode' => 'prefixLangTitle',
                'label' => $llPrefix . 'twitter_description',
                'config' => [
                    'type' => 'text',
                    'cols' => 40,
                    'rows' => 3
                ]
            ],
            'twitter_image' => [
                'exclude' => true,
                'label' => $llPrefix . 'twitter_image',
                'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig(
                    'twitter_image',
                    [
                        // Use the imageoverlayPalette instead of the basicoverlayPalette
                        'overrideChildTca' => [
                            'types' => [
                                '0' => [
                                    'showitem' => '
                                    --palette--;;imageoverlayPalette,
                                    --palette--;;filePalette'
                                ],
                                \TYPO3\CMS\Core\Resource\File::FILETYPE_IMAGE => [
                                    'showitem' => '
                                    --palette--;;imageoverlayPalette,
                                    --palette--;;filePalette'
                                ]
                            ],
                            'columns' => [
                                'crop' => $openGraphCropConfiguration
                            ]
                        ],
                        'behaviour' => [
                            'allowLanguageSynchronization' => true
                        ]
                    ],
                    $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext']
                )
            ],
        ]
    );

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette(
        'pages_language_overlay',
        'seo',
        '
            --linebreak--, seo_title, --linebreak--,
        ',
        'before: description'
    );

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette(
        'pages_language_overlay',
        'yoast-social-og',
        '
            --linebreak--, og_title, --linebreak--, og_description, --linebreak--, og_image,
        '
    );

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette(
        'pages_language_overlay',
        'yoast-social-twitter',
        '
            --linebreak--, twitter_title, --linebreak--, twitter_description, --linebreak--, twitter_image,
        '
    );

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette(
        'pages_language_overlay',
        'yoast-robot',
        '
            no_index;' . $llPrefix . 'pages.no_index_formlabel, no_follow;' . $llPrefix . 'pages.no_follow_formlabel,
        '
    );

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
        'pages_language_overlay',
        '
    --div--;' . $llPrefix . 'pages.tabs.seo,
        --palette--;' . $llPrefix . 'pages.palettes.metadata;seo,
        --palette--;' . $llPrefix . 'pages.palettes.readability;yoast-readability,
        --palette--;' . $llPrefix . 'pages.palettes.focusKeyphrase;yoast-focuskeyword,
        --palette--;' . $llPrefix . 'pages.palettes.focusRelatedKeyphrases;yoast-relatedkeywords,
        --palette--;' . $llPrefix . 'pages.palettes.robot;yoast-robot,
        --palette--;' . $llPrefix . 'pages.palettes.advanced;yoast-advanced,
    --div--;' . $llPrefix . 'pages.tabs.social,
        --palette--;' . $llPrefix . 'pages.palettes.og;yoast-social-og,
        --palette--;' . $llPrefix . 'pages.palettes.twitter;yoast-social-twitter,
    ',
        $dokTypes,
        'after:subtitle'
    );
} else {
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
        'pages_language_overlay',
        '
        --palette--;' . $llPrefix . 'pages.palettes.readability;yoast-readability,
        --palette--;' . $llPrefix . 'pages.palettes.focusKeyphrase;yoast-focuskeyword,
        --palette--;' . $llPrefix . 'pages.palettes.focusRelatedKeyphrases;yoast-relatedkeywords,
    ',
        $dokTypes,
        'after: tx_yoastseo_cornerstone'
    );

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
        'pages_language_overlay',
        '
        --palette--;' . $llPrefix . 'pages.palettes.advances;yoast-advanced,
    ',
        $dokTypes,
        'after: twitter_image'
    );
}
