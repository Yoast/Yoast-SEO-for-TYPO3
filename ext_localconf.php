<?php
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_pagerenderer.php']['render-postProcess'][] = \YoastSeoForTypo3\YoastSeo\Frontend\PageRenderer\PageMetaRenderer::class . '->render';

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/db_layout.php']['drawHeaderHook'][] = \YoastSeoForTypo3\YoastSeo\Backend\PageLayoutHeader::class . '->render';

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptConstants(
    'config.yoast_seo.fe_preview_type = '
        . \YoastSeoForTypo3\YoastSeo\Backend\PageLayoutHeader::FE_PREVIEW_TYPE
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript(
    'YoastSeo',
    'constants',
    '<INCLUDE_TYPOSCRIPT: source="FILE: EXT:yoast_seo/Configuration/TypoScript/constants.txt">'
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript(
    'YoastSeo',
    'setup',
    '<INCLUDE_TYPOSCRIPT: source="FILE: EXT:yoast_seo/Configuration/TypoScript/setup.txt">'
);

$GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1514550050] = array(
    'nodeName' => 'snippetPreview',
    'priority' => 40,
    'class' => \YoastSeoForTypo3\YoastSeo\Form\Element\SnippetPreview::class,
);

$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['yoast_seo'] = array(
    'translations' => array(
        'availableLocales' => array(
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
        ),
        'languageKeyToLocaleMapping' => array(
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
        )
    ),
    'menuActions' => array(
        ['action' => 'dashboard', 'label' => 'dashboard'],
        ['action' => 'edit', 'label' => 'edit'],
        ['action' => 'settings', 'label' => 'settings']
    ),
    'viewSettings' => array(
        'showAnalysisTab' => true,
        'showSocialTab' => true,
        'showAdvancedTab' => true
    ),
    'previewUrlTemplate' => '/index.php?id=%d&type=%d&L=%d'
);

// allow social meta fields to be overlaid
$GLOBALS['TYPO3_CONF_VARS']['FE']['pageOverlayFields'] .=
      ',tx_yoastseo_canonical_url'
    . ',tx_yoastseo_facebook_title'
    . ',tx_yoastseo_facebook_description'
    . ',tx_yoastseo_robot_instructions'
    . ',tx_yoastseo_title'
    . ',tx_yoastseo_twitter_title'
    . ',tx_yoastseo_twitter_description';
