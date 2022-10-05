<?php

defined('TYPO3') || die;

(static function () {
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/db_layout.php']['drawHeaderHook'][]
        = \YoastSeoForTypo3\YoastSeo\Backend\PageLayoutHeader::class . '->render';
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_pagerenderer.php']['render-postProcess'][]
        = \YoastSeoForTypo3\YoastSeo\StructuredData\StructuredDataProviderManager::class . '->render';
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_pagerenderer.php']['render-postProcess'][]
        = \YoastSeoForTypo3\YoastSeo\Frontend\AdditionalPreviewData::class . '->render';
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['usePageCache'][]
        = \YoastSeoForTypo3\YoastSeo\Frontend\UsePageCache::class;
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['TYPO3\CMS\Frontend\Page\PageGenerator']['generateMetaTags']['yoastRecord']
        = \YoastSeoForTypo3\YoastSeo\MetaTag\RecordMetaTagGenerator::class . '->generate';

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript(
        'yoast_seo',
        'setup',
        "@import 'EXT:yoast_seo/Configuration/TypoScript/setup.typoscript'"
    );

    foreach (\YoastSeoForTypo3\YoastSeo\Utility\ConfigurationUtility::getFormEngineNodes() as $nodeKey => $nodeInfo) {
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][$nodeKey] = [
            'nodeName' => $nodeInfo[0],
            'priority' => 40,
            'class' => $nodeInfo[1]
        ];
    }

    $defaultConfiguration = \YoastSeoForTypo3\YoastSeo\Utility\ConfigurationUtility::getDefaultConfiguration();
    \TYPO3\CMS\Core\Utility\ArrayUtility::mergeRecursiveWithOverrule(
        $defaultConfiguration,
        (array)($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['yoast_seo'] ?? [])
    );
    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['yoast_seo'] = $defaultConfiguration;

    $iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
    $iconRegistry->registerIcon(
        'module-yoast',
        \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
        ['source' => 'EXT:yoast_seo/Resources/Public/Images/Yoast-module-container.svg']
    );

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/install']['update']['seoTitleUpdate']
        = \YoastSeoForTypo3\YoastSeo\Install\SeoTitleUpdate::class;
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/install']['update']['canonicalFieldUpdate']
        = \YoastSeoForTypo3\YoastSeo\Install\CanonicalFieldUpdate::class;

    $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['yoastseo_recordcache'] ??= [];
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['yoastseo_recordcache']['groups'] ??= ['system'];
})();
