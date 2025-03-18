<?php

/**
 * This file is part of the "yoast_seo" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

return [
    'ctrl' => [
        'title' => 'Minimal Record Example',
        'label' => 'title',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'languageField' => 'sys_language_uid',
        'iconfile' => 'EXT:sitepackage/Resources/Public/Icons/Icon.svg',
    ],
    'types' => [
        '1' => ['showitem' => 'title, text, sys_language_uid'],
    ],
    'columns' => [
        'title' => [
            'label' => 'Title',
            'config' => [
                'type' => 'input',
                'size' => 30,
            ],
        ],
        'text' => [
            'label' => 'Text',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 15,
                'enableRichtext' => true,
                'richtextConfiguration' => 'minimal',
            ],
        ],
        'sys_language_uid' => [
            'label' => 'Language',
            'config' => [
                'type' => 'language',
            ],
        ],
    ],
];
