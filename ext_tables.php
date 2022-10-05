<?php

defined('TYPO3') || die;

(static function () {
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addModule(
        'yoast',
        '',
        'after:web',
        null,
        [
            'iconIdentifier' => 'module-yoast',
            'labels' => 'LLL:EXT:yoast_seo/Resources/Private/Language/BackendModule.xlf',
            'name' => 'yoast'
        ]
    );

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'YoastSeo',
        'yoast',
        'dashboard',
        '',
        [\YoastSeoForTypo3\YoastSeo\Controller\ModuleController::class => 'dashboard'],
        [
            'access' => 'user,group',
            'icon' => 'EXT:yoast_seo/Resources/Public/Images/Yoast-module-dashboard.svg',
            'labels' => 'LLL:EXT:yoast_seo/Resources/Private/Language/BackendModuleDashboard.xlf',
        ]
    );

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'YoastSeo',
        'yoast',
        'overview',
        '',
        [\YoastSeoForTypo3\YoastSeo\Controller\OverviewController::class => 'list'],
        [
            'access' => 'user,group',
            'icon' => 'EXT:yoast_seo/Resources/Public/Images/Yoast-module-overview.svg',
            'labels' => 'LLL:EXT:yoast_seo/Resources/Private/Language/BackendModuleOverview.xlf',
        ]
    );

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'YoastSeo',
        'yoast',
        'premium',
        '',
        [\YoastSeoForTypo3\YoastSeo\Controller\ModuleController::class => 'premium'],
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
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToUserSettings(
        '--div--;LLL:EXT:yoast_seo/Resources/Private/Language/BackendModule.xlf:usersettings.title,hideYoastInPageModule'
    );

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_pagerenderer.php']['render-preProcess'][]
        = \YoastSeoForTypo3\YoastSeo\Hooks\BackendYoastConfig::class . '->renderConfig';
})();
