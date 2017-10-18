<?php
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns(
    'pages_language_overlay',
    array(
        'tx_yoastseo_title' => array(
            'label' => 'SEO title',
            'config' => array(
                'type' => 'input'
            )
        ),
        'tx_yoastseo_focuskeyword' => array(
            'label' => 'SEO focus keyword',
            'config' => array(
                'type' => 'input'
            )
        ),
        'tx_yoastseo_canonical_url' => array(
            'label' => 'Canonical URL',
            'config' => array(
                'type' => 'input'
            )
        ),
        'tx_yoastseo_facebook_title' => array(
            'label' => 'Facebook title',
            'config' => array(
                'type' => 'input'
            )
        ),
        'tx_yoastseo_facebook_description' => array(
            'label' => 'Facebook description',
            'config' => array(
                'type' => 'input'
            )
        ),
        'tx_yoastseo_robot_instructions' => array(
            'label' => 'Robot instructions',
            'config' => array(
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => array(
                    array('index, follow', 0),
                    array('noindex, nofollow', 1),
                    array('noindex, follow', 2),
                    array('index, nofollow', 3),
                )
            )
        ),
        'tx_yoastseo_facebook_image' => array(
            'label' => 'Facebook image',
            'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig(
                'tx_yoastseo_facebook_image',
                array(
                    // Use the imageoverlayPalette instead of the basicoverlayPalette
                    'foreign_types' => array(
                        '0' => array(
                            'showitem' => '
                                --palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                                --palette--;;filePalette'
                        ),
                        \TYPO3\CMS\Core\Resource\File::FILETYPE_TEXT => array(
                            'showitem' => '
                                --palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                                --palette--;;filePalette'
                        ),
                        \TYPO3\CMS\Core\Resource\File::FILETYPE_IMAGE => array(
                            'showitem' => '
                                --palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                                --palette--;;filePalette'
                        ),
                        \TYPO3\CMS\Core\Resource\File::FILETYPE_AUDIO => array(
                            'showitem' => '
                                --palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.audioOverlayPalette;audioOverlayPalette,
                                --palette--;;filePalette'
                        ),
                        \TYPO3\CMS\Core\Resource\File::FILETYPE_VIDEO => array(
                            'showitem' => '
                                --palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.videoOverlayPalette;videoOverlayPalette,
                                --palette--;;filePalette'
                        ),
                        \TYPO3\CMS\Core\Resource\File::FILETYPE_APPLICATION => array(
                            'showitem' => '
                                --palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                                --palette--;;filePalette'
                        )
                    )
                )
            )
        ),
        'tx_yoastseo_twitter_title' => array(
            'label' => 'Twitter title',
            'config' => array(
                'type' => 'input'
            )
        ),
        'tx_yoastseo_twitter_description' => array(
            'label' => 'Twitter description',
            'config' => array(
                'type' => 'input'
            )
        ),
        'tx_yoastseo_twitter_image' => array(
            'label' => 'Twitter image',
            'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig(
                'tx_yoastseo_twitter_image',
                array(
                    // Use the imageoverlayPalette instead of the basicoverlayPalette
                    'foreign_types' => array(
                        '0' => array(
                            'showitem' => '
                                --palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                                --palette--;;filePalette'
                        ),
                        \TYPO3\CMS\Core\Resource\File::FILETYPE_TEXT => array(
                            'showitem' => '
                                --palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                                --palette--;;filePalette'
                        ),
                        \TYPO3\CMS\Core\Resource\File::FILETYPE_IMAGE => array(
                            'showitem' => '
                                --palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                                --palette--;;filePalette'
                        ),
                        \TYPO3\CMS\Core\Resource\File::FILETYPE_AUDIO => array(
                            'showitem' => '
                                --palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.audioOverlayPalette;audioOverlayPalette,
                                --palette--;;filePalette'
                        ),
                        \TYPO3\CMS\Core\Resource\File::FILETYPE_VIDEO => array(
                            'showitem' => '
                                --palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.videoOverlayPalette;videoOverlayPalette,
                                --palette--;;filePalette'
                        ),
                        \TYPO3\CMS\Core\Resource\File::FILETYPE_APPLICATION => array(
                            'showitem' => '
                                --palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                                --palette--;;filePalette'
                        )
                    )
                )
            )
        )
    )
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette(
    'pages_language_overlay',
    'metatags',
    '
    --linebreak--, tx_yoastseo_title, tx_yoastseo_focuskeyword, tx_yoastseo_canonical_url, tx_yoastseo_robot_instructions,
     --linebreak--, tx_yoastseo_facebook_title, 
     --linebreak--, tx_yoastseo_facebook_description, 
     --linebreak--, tx_yoastseo_facebook_image,
     --linebreak--, tx_yoastseo_twitter_title, 
     --linebreak--, tx_yoastseo_twitter_description, 
     --linebreak--, tx_yoastseo_twitter_image
    '
);
