<?php

$llPrefix = 'LLL:EXT:yoast_seo/Resources/Private/Language/TCA.xlf:';

return [
    'ctrl' => [
        'title' => $llPrefix . 'tx_yoastseo_related_focuskeyword.title',
        'label' => 'keyword',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'translationSource' => 'l10n_source',
        'sortby' => 'sorting',
        'iconfile' => 'EXT:yoast_seo/Resources/Public/Icons/Extension.svg',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
        ],
        'versioningWS' => true,
        'origUid' => 't3_origuid',
        'security' => [
            'ignorePageTypeRestriction' => true,
        ],
    ],
    'columns' => [
        'sys_language_uid' => $GLOBALS['TCA']['tt_content']['columns']['sys_language_uid'],
        'l10n_parent' => array_replace_recursive($GLOBALS['TCA']['tt_content']['columns']['l18n_parent'], [
            'config' => [
                'foreign_table' => 'tx_yoastseo_related_focuskeyword',
                'foreign_table_where' => 'AND tx_yoastseo_related_focuskeyword.pid=###CURRENT_PID### AND tx_yoastseo_related_focuskeyword.sys_language_uid IN (-1,0)',
            ]
        ]),
        'l10n_source' => $GLOBALS['TCA']['tt_content']['columns']['l10n_source'],
        'l10n_diffsource' => $GLOBALS['TCA']['tt_content']['columns']['l18n_diffsource'],
        'hidden' => $GLOBALS['TCA']['tt_content']['columns']['hidden'],
        'keyword' => [
            'exclude' => 1,
            'l10n_mode' => 'prefixLangTitle',
            'label' => $llPrefix . 'tx_yoastseo_related_focuskeyword.fields.keyword',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'required',
                'required' => true,
            ]
        ],
        'synonyms' => [
            'exclude' => 1,
            'l10n_mode' => 'prefixLangTitle',
            'label' => $llPrefix . 'tx_yoastseo_related_focuskeyword.fields.synonyms',
            'config' => [
                'type' => 'input',
                'size' => 30,
            ]
        ],
        'analysis' => [
            'exclude' => 1,
            'displayCond' => 'FIELD:keyword:REQ:TRUE',
            'label' => $llPrefix . 'tx_yoastseo_related_focuskeyword.fields.analysis',
            'config' => [
                'type' => 'none',
                'renderType' => 'focusKeywordAnalysis',
            ]
        ],
        'uid_foreign' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        'tablenames' => [
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
                --div--;General, --palette--;;yoast-focuskeyword,
                --div--;Visibility, sys_language_uid, l10n_parent,l10n_diffsource, uid_foreign, tablenames, hidden
            '
        ]
    ],
];
