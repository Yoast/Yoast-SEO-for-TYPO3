<?php

use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use YoastSeoForTypo3\YoastSeo\Backend\PageLayoutHeader;
use YoastSeoForTypo3\YoastSeo\Frontend\AdditionalPreviewData;
use YoastSeoForTypo3\YoastSeo\Frontend\UsePageCache;
use YoastSeoForTypo3\YoastSeo\MetaTag\AdvancedRobotsGenerator;
use YoastSeoForTypo3\YoastSeo\MetaTag\RecordMetaTagGenerator;
use YoastSeoForTypo3\YoastSeo\StructuredData\StructuredDataProviderManager;
use YoastSeoForTypo3\YoastSeo\Updates\MigrateDashboardWidget;
use YoastSeoForTypo3\YoastSeo\Updates\MigratePremiumFocusKeywords;
use YoastSeoForTypo3\YoastSeo\Updates\MigrateRedirects;
use YoastSeoForTypo3\YoastSeo\Utility\ConfigurationUtility;

defined('TYPO3') || die;

(static function () {
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/db_layout.php']['drawHeaderHook'][]
        = PageLayoutHeader::class . '->render';
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_pagerenderer.php']['render-postProcess'][]
        = StructuredDataProviderManager::class . '->render';
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_pagerenderer.php']['render-postProcess'][]
        = AdditionalPreviewData::class . '->render';
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['usePageCache'][]
        = UsePageCache::class;
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['TYPO3\CMS\Frontend\Page\PageGenerator']['generateMetaTags']['yoastRecord']
        = RecordMetaTagGenerator::class . '->generate';
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['TYPO3\CMS\Frontend\Page\PageGenerator']['generateMetaTags']['advancedrobots'] =
        AdvancedRobotsGenerator::class . '->generate';

    ExtensionManagementUtility::addTypoScript(
        'yoast_seo',
        'setup',
        "@import 'EXT:yoast_seo/Configuration/TypoScript/setup.typoscript'"
    );

    foreach (ConfigurationUtility::getFormEngineNodes() as $nodeKey => $nodeInfo) {
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][$nodeKey] = [
            'nodeName' => $nodeInfo[0],
            'priority' => 40,
            'class' => $nodeInfo[1],
        ];
    }

    $defaultConfiguration = ConfigurationUtility::getDefaultConfiguration();
    ArrayUtility::mergeRecursiveWithOverrule(
        $defaultConfiguration,
        (array)($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['yoast_seo'] ?? [])
    );
    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['yoast_seo'] = $defaultConfiguration;

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/install']['update']['yoastRedirectsMigrate']
        = MigrateRedirects::class;
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/install']['update']['yoastPremiumFocusKeywordsMigrate']
        = MigratePremiumFocusKeywords::class;
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/install']['update']['yoastDashboardWidgetMigrate']
        = MigrateDashboardWidget::class;

    $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['yoastseo_recordcache'] ??= [];
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['yoastseo_recordcache']['groups'] ??= ['system'];
})();
