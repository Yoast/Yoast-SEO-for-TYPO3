<?php

$llPrefix = 'LLL:EXT:yoast_seo/Resources/Private/Language/TCA.xlf:';

return [
    'ctrl' => [
        'title' => $llPrefix . 'tx_yoastseo_prominent_word.title',
        'label' => 'stem',
        'languageField' => 'sys_language_uid',
        'iconfile' => 'EXT:yoast_seo/Resources/Public/Icons/Extension.svg',
        'hideTable' => true
    ],
    'columns' => [
        'stem' => [
            'label' => $llPrefix . 'tx_yoastseo_prominent_word.fields.stem',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'required',
            ]
        ],
        'table' => [
            'label' => $llPrefix . 'tx_yoastseo_prominent_word.fields.table',
            'config' => [
                'type' => 'input',
                'size' => 30,
            ]
        ],
        'weight' => [
            'label' => $llPrefix . 'tx_yoastseo_prominent_word.fields.weight',
            'config' => [
                'type' => 'input',
            ]
        ],
    ],
    'palettes' => [
        'yoast-prominentword' => [
            'showitem' => '
                --linebreak--, stem,
                --linebreak--, table,
                --linebreak--, weight'
        ]
    ],
    'types' => [
        '0' => [
            'showitem' => '
                --div--;General, --palette--;;yoast-prominentword,
                --div--;Visibility, sys_language_uid
            '
        ]
    ],
];
