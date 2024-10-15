<?php

use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;
use YoastSeoForTypo3\YoastSeo\Controller\CrawlerController;
use YoastSeoForTypo3\YoastSeo\Controller\DashboardController;
use YoastSeoForTypo3\YoastSeo\Controller\OverviewController;
use YoastSeoForTypo3\YoastSeo\Hooks\BackendYoastConfig;

defined('TYPO3') || die;

(static function () {
    $typo3Version = GeneralUtility::makeInstance(Typo3Version::class);
    if ($typo3Version->getMajorVersion() < 12) {
        ExtensionManagementUtility::addModule(
            'yoast', '', 'after:web', null, [
                'iconIdentifier' => 'module-yoast',
                'labels' => 'LLL:EXT:yoast_seo/Resources/Private/Language/BackendModule.xlf',
                'name' => 'yoast'
            ]
        );
        ExtensionManagementUtility::addCoreNavigationComponent(
            'yoast',
            'TYPO3/CMS/Backend/PageTree/PageTreeElement'
        );

        ExtensionUtility::registerModule(
            'YoastSeo',
            'yoast',
            'dashboard',
            '',
            [DashboardController::class => 'index'],
            [
                'access' => 'user,group',
                'iconIdentifier' => 'module-yoast-dashboard',
                'labels' => 'LLL:EXT:yoast_seo/Resources/Private/Language/BackendModuleDashboard.xlf',
                'inheritNavigationComponentFromMainModule' => false
            ]
        );

        ExtensionUtility::registerModule(
            'YoastSeo',
            'yoast',
            'overview',
            '',
            [OverviewController::class => 'list'],
            [
                'access' => 'user,group',
                'iconIdentifier' => 'module-yoast-overview',
                'labels' => 'LLL:EXT:yoast_seo/Resources/Private/Language/BackendModuleOverview.xlf',
            ]
        );

        ExtensionUtility::registerModule(
            'YoastSeo',
            'yoast',
            'crawler',
            '',
            [CrawlerController::class => 'index,resetProgress'],
            [
                'access' => 'user,group',
                'iconIdentifier' => 'module-yoast-crawler',
                'labels' => 'LLL:EXT:yoast_seo/Resources/Private/Language/BackendModuleCrawler.xlf',
                'inheritNavigationComponentFromMainModule' => false
            ]
        );

        ExtensionManagementUtility::allowTableOnStandardPages(
            'tx_yoastseo_related_focuskeyword'
        );
        ExtensionManagementUtility::allowTableOnStandardPages(
            'tx_yoastseo_prominent_word'
        );
    }

    // Extend user settings
    $GLOBALS['TYPO3_USER_SETTINGS']['columns']['hideYoastInPageModule'] = [
        'label' => 'LLL:EXT:yoast_seo/Resources/Private/Language/BackendModule.xlf:usersettings.hideYoastInPageModule',
        'type' => 'check'
    ];
    ExtensionManagementUtility::addFieldsToUserSettings(
        '--div--;LLL:EXT:yoast_seo/Resources/Private/Language/BackendModule.xlf:usersettings.title,hideYoastInPageModule'
    );

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_pagerenderer.php']['render-preProcess'][]
        = BackendYoastConfig::class . '->renderConfig';
})();
