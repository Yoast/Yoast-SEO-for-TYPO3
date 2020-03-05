<?php
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/db_layout.php']['drawHeaderHook'][]
    = \YoastSeoForTypo3\YoastSeo\Backend\PageLayoutHeader::class . '->render';
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_pagerenderer.php']['render-postProcess'][]
    = \YoastSeoForTypo3\YoastSeo\StructuredData\StructuredDataProviderManager::class . '->render';
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['usePageCache'][]
    = \YoastSeoForTypo3\YoastSeo\Frontend\UsePageCache::class;

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptConstants(
    'config.yoast_seo.fe_preview_type = '
        . \YoastSeoForTypo3\YoastSeo\Backend\PageLayoutHeader::FE_PREVIEW_TYPE . PHP_EOL .
    'config.yoast_seo.sitemap_xml_type = '
        . \YoastSeoForTypo3\YoastSeo\UserFunctions\XmlSitemap::DOKTYPE
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript(
    'YoastSeo',
    'constants',
    '<INCLUDE_TYPOSCRIPT: source="FILE: EXT:yoast_seo/Configuration/TypoScript/constants.typoscript">'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript(
    'YoastSeo',
    'setup',
    '<INCLUDE_TYPOSCRIPT: source="FILE: EXT:yoast_seo/Configuration/TypoScript/setup.typoscript">'
);

$GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1514550050] = array(
    'nodeName' => 'snippetPreview',
    'priority' => 40,
    'class' => \YoastSeoForTypo3\YoastSeo\Form\Element\SnippetPreview::class,
);

$GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1514728465] = array(
    'nodeName' => 'readabilityAnalysis',
    'priority' => 40,
    'class' => \YoastSeoForTypo3\YoastSeo\Form\Element\ReadabilityAnalysis::class,
);

$GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1514830899] = array(
    'nodeName' => 'focusKeywordAnalysis',
    'priority' => 40,
    'class' => \YoastSeoForTypo3\YoastSeo\Form\Element\FocusKeywordAnalysis::class,
);

$GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1552342645] = array(
    'nodeName' => 'cornerstone',
    'priority' => 43,
    'class' => \YoastSeoForTypo3\YoastSeo\Form\Element\Cornerstone::class,
);

$GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1537991862] = array(
    'nodeName' => 'hiddenField',
    'priority' => 40,
    'class' => \YoastSeoForTypo3\YoastSeo\Form\Element\HiddenField::class
);

if (!\YoastSeoForTypo3\YoastSeo\Utility\YoastUtility::isPremiumInstalled()) {
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1553888878] = array(
        'nodeName' => 'synonyms',
        'priority' => 40,
        'class' => \YoastSeoForTypo3\YoastSeo\Form\Element\Synonyms::class
    );

    $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1553977739] = array(
        'nodeName' => 'relatedKeyphrases',
        'priority' => 40,
        'class' => \YoastSeoForTypo3\YoastSeo\Form\Element\RelatedKeyphrases::class
    );

    $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1554381790] = array(
        'nodeName' => 'internalLinkingSuggestion',
        'priority' => 40,
        'class' => \YoastSeoForTypo3\YoastSeo\Form\Element\InternalLinkingSuggestion::class
    );
}

$llFolder = 'LLL:EXT:yoast_seo/Resources/Private/Language/';

$defaultConfiguration = [
    'allowedDoktypes' => [
        'page' => 1,
        'backend_section' => 5
    ],
    'translations' => [
        'availableLocales' => [
            'bg_BG',
            'ca',
            'da_DK',
            'de_DE',
            'en_AU',
            'en_GB',
            'es_ES',
            'es_MX',
            'fa_IR',
            'fi',
            'fr_FR',
            'he_IL',
            'hr',
            'id_ID',
            'it_IT',
            'ja',
            'nb_NO',
            'nl_NL',
            'pl_PL',
            'pt_BR',
            'pt_PT',
            'ru_RU',
            'sk_SK',
            'sv_SE',
            'tr_TR'
        ],
        'languageKeyToLocaleMapping' => [
            'bg' => 'bg_BG',
            'da' => 'da_DK',
            'de' => 'de_DE',
            'en' => 'en_GB',
            'es' => 'es_ES',
            'fa' => 'fa_IR',
            'fr' => 'fr_FR',
            'he' => 'he_IL',
            'it' => 'it_IT',
            'no' => 'nb_NO',
            'nl' => 'nl_NL',
            'pl' => 'pl_PL',
            'pt' => 'pt_PT',
            'ru' => 'ru_RU',
            'sk' => 'sk_SK',
            'sv' => 'sv_SE',
            'tr' => 'tr_TR'
        ]
    ],
    'menuActions' => [
        ['action' => 'dashboard', 'label' => 'dashboard'],
        ['action' => 'update', 'label' => 'update']
    ],
    'viewSettings' => [
        'showAnalysisTab' => true,
        'showSocialTab' => true,
        'showAdvancedTab' => true
    ],
    'previewUrlTemplate' => '/?id=%d&type=%d&L=%d',
    'overview_filters' => [
        '10' => [
            'key' => 'cornerstone',
            'label' => $llFolder . 'BackendModuleOverview.xlf:cornerstoneContent',
            'description' => $llFolder . 'BackendModuleOverview.xlf:cornerstoneContent.description',
            'link' => 'https://yoa.st/typo3-cornerstone-content',
            'dataProvider' => \YoastSeoForTypo3\YoastSeo\DataProviders\CornerstoneOverviewDataProvider::class . '->process',
            'countProvider' => \YoastSeoForTypo3\YoastSeo\DataProviders\CornerstoneOverviewDataProvider::class . '->numberOfItems'
        ],
        '20' => [
            'key' => 'withoutDescription',
            'label' => $llFolder . 'BackendModuleOverview.xlf:withoutDescription',
            'description' => $llFolder . 'BackendModuleOverview.xlf:withoutDescription.description',
            'link' => 'https://yoa.st/typo3-meta-description',
            'dataProvider' => YoastSeoForTypo3\YoastSeo\DataProviders\PagesWithoutDescriptionOverviewDataProvider::class . '->process',
            'countProvider' => YoastSeoForTypo3\YoastSeo\DataProviders\PagesWithoutDescriptionOverviewDataProvider::class . '->numberOfItems'
        ],
    ]
];

$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['yoast_seo'] = array_merge_recursive(
    $defaultConfiguration,
    (array)$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['yoast_seo']
);

// allow social meta fields to be overlaid
$GLOBALS['TYPO3_CONF_VARS']['FE']['pageOverlayFields'] .=
    ',canonical_url'
    . ',og_title'
    . ',og_description'
    . ',tx_yoastseo_robot_instructions'
    . ',seo_title'
    . ',twitter_title'
    . ',twitter_description';

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

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptSetup(trim('
    config.structuredData.providers {
        breadcrumb {
            provider = YoastSeoForTypo3\YoastSeo\StructuredData\BreadcrumbStructuredDataProvider
            after = site
            excludedDoktypes =
        }
        site {
            provider = YoastSeoForTypo3\YoastSeo\StructuredData\SiteStructuredDataProvider
        }
    }
'));
