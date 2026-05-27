<?php

/**
 * This file is part of the "yoast_seo" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

defined('TYPO3') || die;

if ((new Typo3Version())->getMajorVersion() >= 14) {
    // @todo: using addUserSetting removed the custom Yoast SEO tab, maybe restore this in the future if needed?

    ExtensionManagementUtility::addUserSetting(
        'hideYoastInPageModule',
        [
            'label' => 'LLL:EXT:yoast_seo/Resources/Private/Language/BackendModule.xlf:usersettings.hideYoastInPageModule',
            'config' => [
                'renderType' => 'checkboxToggle',
                'type' => 'check',
            ],
        ],
    );
}
