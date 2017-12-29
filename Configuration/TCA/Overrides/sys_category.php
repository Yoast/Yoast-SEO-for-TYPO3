<?php
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns(
    'sys_category',
    [
        'tx_yoastseo_snippetpreview' => [
            'config' => [
                'type' => 'text',
                'renderType' => 'snippetPreview',
                'settings' => [
                    'titleField' => 'title',
                    'descriptionField' => 'description'
                ]
            ]
        ],
    ]
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
    'sys_category',
    '
    --linebreak--, tx_yoastseo_snippetpreview,
    ',
    '',
    'before:title'
);
