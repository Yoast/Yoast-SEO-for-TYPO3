<?php

if (TYPO3_MODE === 'BE') {
    $offset = 0;
    foreach ($GLOBALS['TBE_MODULES'] as $key => $_) {
        if ($key === 'web') {
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

    $moduleController = \YoastSeoForTypo3\YoastSeo\Controller\ModuleController::class;
    $overviewController = \YoastSeoForTypo3\YoastSeo\Controller\OverviewController::class;
    if (\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Information\Typo3Version::class)
            ->getMajorVersion() < 10) {
        $moduleController = 'Module';
        $overviewController = 'Overview';
    }

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'YoastSeoForTypo3.yoast_seo',
        'yoast',
        'dashboard',
        '',
        [
            $moduleController => 'dashboard',
        ],
        [
            'access' => 'user,group',
            'icon' => 'EXT:yoast_seo/Resources/Public/Images/Yoast-module-dashboard.svg',
            'labels' => 'LLL:EXT:yoast_seo/Resources/Private/Language/BackendModuleDashboard.xlf',
        ]
    );

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'YoastSeoForTypo3.yoast_seo',
        'yoast',
        'overview',
        '',
        [
            $overviewController => 'list',
        ],
        [
            'access' => 'user,group',
            'icon' => 'EXT:yoast_seo/Resources/Public/Images/Yoast-module-overview.svg',
            'labels' => 'LLL:EXT:yoast_seo/Resources/Private/Language/BackendModuleOverview.xlf',
        ]
    );

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'YoastSeoForTypo3.yoast_seo',
        'yoast',
        'premium',
        '',
        [
            $moduleController => 'premium',
        ],
        [
            'access' => 'user,group',
            'icon' => 'EXT:yoast_seo/Resources/Public/Images/Yoast-module-premium.svg',
            'labels' => 'LLL:EXT:yoast_seo/Resources/Private/Language/BackendModulePremium.xlf',
        ]
    );

    // Extend user settings
    $GLOBALS['TYPO3_USER_SETTINGS']['columns']['hideYoastInPageModule'] = [
        'label' => 'LLL:EXT:yoast_seo/Resources/Private/Language/BackendModule.xlf:usersettings.hideYoastInPageModule',
        'type' => 'check'
    ];
    $GLOBALS['TYPO3_USER_SETTINGS']['showitem'] .= ',
            --div--;LLL:EXT:yoast_seo/Resources/Private/Language/BackendModule.xlf:usersettings.title,hideYoastInPageModule';
}
