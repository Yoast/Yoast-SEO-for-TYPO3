<?php
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_pagerenderer.php']['render-postProcess'][] = \YoastSeoForTypo3\YoastSeo\Frontend\MetaService\PageMetaRenderer::class . '->render';

/** @var \YoastSeoForTypo3\YoastSeo\Frontend\MetaService\PageMetaRenderer $pageMetaRenderer */
$pageMetaRenderer = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\YoastSeoForTypo3\YoastSeo\Frontend\MetaService\PageMetaRenderer::class);

$pageMetaRenderer->registerService(\YoastSeoForTypo3\YoastSeo\Frontend\MetaService\CanonicalTagService::class);

unset($pageMetaRenderer);