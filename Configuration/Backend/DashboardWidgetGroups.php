<?php

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

if (!ExtensionManagementUtility::isLoaded('widgets')) {
    return [
        'seo' => [
            'title' => 'SEO',
        ],
    ];
}
