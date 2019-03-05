<?php
$llPrefix = 'LLL:EXT:yoast_seo/Resources/Private/Language/BackendModule.xlf:';

return [
    'ctrl' => [
        'title' => $llPrefix . 'tx_yoast_seo_premium_focus_keywords.title',
        'label' => 'keyword',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'translationSource' => 'l10n_source',
        'sortby' => 'sorting',
        'iconfile' => 'EXT:yoast_seo_premium/Resources/Public/Icons/Extension.svg',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
        ],
        'versioningWS' => true,
        'origUid' => 't3_origuid',
    ],
    'columns' => [
        'sys_language_uid' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.language',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'sys_language',
                'foreign_table_where' => 'ORDER BY sys_language.title',
                'items' => [
                    ['LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.allLanguages', -1],
                    ['LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.default_value', 0]
                ],
                'default' => 0,
                'fieldWizard' => [
                    'selectIcons' => [
                        'disabled' => false,
                    ],
                ],
            ]
        ],
        'l10n_parent' => [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'exclude' => 1,
            'label' => 'Translation parent',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['', 0],
                ],
                'foreign_table' => 'tx_yoast_seo_premium_focus_keywords',
                'foreign_table_where' => 'AND tx_yoast_seo_premium_focus_keywords.pid=###CURRENT_PID### AND tx_yoast_seo_premium_focus_keywords.sys_language_uid IN (-1,0)',
            ]
        ],
        'l10n_source' => [
            'exclude' => true,
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'label' => 'Translation source',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [
                        '',
                        0
                    ]
                ],
                'foreign_table' => 'tx_yoast_seo_premium_focus_keywords',
                'foreign_table_where' => 'AND tx_yoast_seo_premium_focus_keywords.pid=###CURRENT_PID### AND tx_yoast_seo_premium_focus_keywords.uid!=###THIS_UID###',
                'default' => 0
            ]
        ],
        'l10n_diffsource' => [
            'config' => [
                'type' => 'passthrough'
            ]
        ],
        'hidden' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.hidden',
            'config' => [
                'type' => 'check',
                'default' => '0'
            ]
        ],
        'keyword' => [
            'exclude' => 1,
            'l10n_mode' => 'prefixLangTitle',
            'label' => $llPrefix . 'tx_yoast_seo_premium_focus_keywords.fields.keyword',
            'config' => [
                'type' => 'input',
                'size' => '30',
                'eval' => 'required',
            ]
        ],
        'synonyms' => [
            'exclude' => 1,
            'l10n_mode' => 'prefixLangTitle',
            'label' => $llPrefix . 'tx_yoast_seo_premium_focus_keywords.fields.synonyms',
            'config' => [
                'type' => 'input',
                'size' => '30',
            ]
        ],
        'analysis' => [
            'exclude' => 1,
            'displayCond' => 'FIELD:keyword:REQ:TRUE',
            'label' => $llPrefix . 'tx_yoast_seo_premium_focus_keywords.fields.analysis',
            'config' => [
                'type' => 'input',
                'renderType' => 'focusKeywordAnalysis',
            ]
        ],
        'parentid' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        'parenttable' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
    ],
    'palettes' => [
        'yoast-focuskeyword' => [
            'showitem' => '
                --linebreak--, keyword,
                --linebreak--, synonyms,
                --linebreak--, analysis'
        ]
    ],
    'types' => [
        '0' => [
            'showitem' => '
                --div--;General, --palette;;yoast-focuskeyword,
                --div--;Visibility, sys_language_uid, l10n_parent,l10n_diffsource, parentid, parenttable, hidden
            '
        ]
    ],
];
