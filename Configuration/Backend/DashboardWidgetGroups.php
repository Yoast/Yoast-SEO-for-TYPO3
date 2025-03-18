<?php

/**
 * This file is part of the "yoast_seo" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

if (!ExtensionManagementUtility::isLoaded('widgets')) {
    return [
        'seo' => [
            'title' => 'SEO',
        ],
    ];
}
