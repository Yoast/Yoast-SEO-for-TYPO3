<?php
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns(
    'pages_language_overlay',
    array(
        'tx_yoastseo_focuskeyword' => array(
            'label' => 'SEO focus keyword',
            'config' => array(
                'type' => 'input'
            )
        )
    )
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette(
    'pages_language_overlay',
    'metatags',
    '--linebreak--, tx_yoastseo_focuskeyword'
);