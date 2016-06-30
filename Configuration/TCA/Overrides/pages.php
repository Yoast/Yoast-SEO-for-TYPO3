<?php
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns(
    'pages',
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
    'pages',
    'metatags',
    '--linebreak--, tx_yoastseo_focuskeyword'
);