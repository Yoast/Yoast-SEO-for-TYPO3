<?php
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_pagerenderer.php']['render-postProcess'][] = \YoastSeoForTypo3\YoastSeo\Frontend\PageRenderer\PageMetaRenderer::class . '->render';

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/db_layout.php']['drawHeaderHook'][] = \YoastSeoForTypo3\YoastSeo\Backend\PageLayoutHeader::class . '->render';

/** @var \YoastSeoForTypo3\YoastSeo\Frontend\PageRenderer\PageMetaRenderer $pageMetaRenderer */
$pageMetaRenderer = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\YoastSeoForTypo3\YoastSeo\Frontend\PageRenderer\PageMetaRenderer::class);

$pageMetaRenderer->registerService(\YoastSeoForTypo3\YoastSeo\Frontend\MetaService\CanonicalTagService::class);
$pageMetaRenderer->registerService(\YoastSeoForTypo3\YoastSeo\Frontend\MetaService\SocialTagService::class);

unset($pageMetaRenderer);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptConstants('
    config.yoast_seo.fe_preview_type = ' . \YoastSeoForTypo3\YoastSeo\Backend\PageLayoutHeader::FE_PREVIEW_TYPE . '
');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript(
    'YoastSeo',
    'setup',
    '<INCLUDE_TYPOSCRIPT: source="FILE: EXT:yoast_seo/Configuration/TypoScript/setup.txt">',
    'defaultContentRendering'
);