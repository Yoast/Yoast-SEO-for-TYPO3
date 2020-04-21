<?php

if (TYPO3_MODE === 'BE') {
    $_EXTKEY = 'yoast_seo';

    $offset = 0;
    foreach ($GLOBALS['TBE_MODULES'] as $key => $_) {
        if ($key == 'web') {
            $GLOBALS['TBE_MODULES'] = array_slice($GLOBALS['TBE_MODULES'], 0, ($offset + 1), true) +
                ['yoast' => ''] +
                array_slice($GLOBALS['TBE_MODULES'], $offset + 1, count($GLOBALS['TBE_MODULES']) - 1, true);
        }
        $offset++;
    }

    $GLOBALS['TBE_MODULES']['_configuration']['yoast'] = [
        'iconIdentifier' => 'module-yoast',
        'labels' => 'LLL:EXT:yoast_seo/Resources/Private/Language/BackendModule.xlf',
        'name' => 'yoast'
    ];

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'YoastSeoForTypo3.' . $_EXTKEY,
        'yoast',
        'dashboard',
        '',
        array(
            'Module' => 'dashboard',
        ),
        array(
            'access' => 'user,group',
            'icon' => 'EXT:' . $_EXTKEY . '/Resources/Public/Images/Yoast-module-dashboard.svg',
            'labels' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/BackendModuleDashboard.xlf',
        )
    );

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'YoastSeoForTypo3.' . $_EXTKEY,
        'yoast',
        'overview',
        '',
        array(
            'Overview' => 'list',
        ),
        array(
            'access' => 'user,group',
            'icon' => 'EXT:' . $_EXTKEY . '/Resources/Public/Images/Yoast-module-overview.svg',
            'labels' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/BackendModuleOverview.xlf',
        )
    );

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'YoastSeoForTypo3.' . $_EXTKEY,
        'yoast',
        'premium',
        '',
        array(
            'Module' => 'premium',
        ),
        array(
            'access' => 'user,group',
            'icon' => 'EXT:' . $_EXTKEY . '/Resources/Public/Images/Yoast-module-premium.svg',
            'labels' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/BackendModulePremium.xlf',
        )
    );

    // Extend user settings
    $GLOBALS['TYPO3_USER_SETTINGS']['columns']['hideYoastInPageModule'] = [
        'label' => 'LLL:EXT:yoast_seo/Resources/Private/Language/BackendModule.xlf:usersettings.hideYoastInPageModule',
        'type' => 'check'
    ];
    $GLOBALS['TYPO3_USER_SETTINGS']['showitem'] .= ',
            --div--;LLL:EXT:yoast_seo/Resources/Private/Language/BackendModule.xlf:usersettings.title,hideYoastInPageModule';
}
