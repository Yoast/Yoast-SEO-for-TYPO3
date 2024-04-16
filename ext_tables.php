<?php

defined('TYPO3') || die;

(static function () {
    $typo3Version = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Information\Typo3Version::class);

    if ($typo3Version->getMajorVersion() < 12) {
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
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addCoreNavigationComponent(
            'yoast',
            'TYPO3/CMS/Backend/PageTree/PageTreeElement'
        );

        $legacyAction = $typo3Version->getMajorVersion() === 10;

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
            'YoastSeo',
            'yoast',
            'dashboard',
            '',
            [\YoastSeoForTypo3\YoastSeo\Controller\DashboardController::class => $legacyAction ? 'legacy' : 'index'],
            [
                'access' => 'user,group',
                'iconIdentifier' => 'module-yoast-dashboard',
                'labels' => 'LLL:EXT:yoast_seo/Resources/Private/Language/BackendModuleDashboard.xlf',
                'inheritNavigationComponentFromMainModule' => false
            ]
        );

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
            'YoastSeo',
            'yoast',
            'overview',
            '',
            [\YoastSeoForTypo3\YoastSeo\Controller\OverviewController::class => $legacyAction ? 'legacy' : 'list'],
            [
                'access' => 'user,group',
                'iconIdentifier' => 'module-yoast-overview',
                'labels' => 'LLL:EXT:yoast_seo/Resources/Private/Language/BackendModuleOverview.xlf',
            ]
        );

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
            'YoastSeo',
            'yoast',
            'crawler',
            '',
            [\YoastSeoForTypo3\YoastSeo\Controller\CrawlerController::class => ($legacyAction ? 'legacy' : 'index') . ',resetProgress'],
            [
                'access' => 'user,group',
                'iconIdentifier' => 'module-yoast-crawler',
                'labels' => 'LLL:EXT:yoast_seo/Resources/Private/Language/BackendModuleCrawler.xlf',
                'inheritNavigationComponentFromMainModule' => false
            ]
        );


        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages(
            'tx_yoastseo_related_focuskeyword'
        );
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages(
            'tx_yoastseo_prominent_word'
        );
    }

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
