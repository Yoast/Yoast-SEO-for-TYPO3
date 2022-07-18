<?php
$llPrefix = 'LLL:EXT:yoast_seo/Resources/Private/Language/BackendModule.xlf:';

\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\YoastSeoForTypo3\YoastSeo\Service\TcaService::class)
    ->addYoastFields(
        'pages',
        \YoastSeoForTypo3\YoastSeo\Utility\YoastUtility::getAllowedDoktypes(null, true)
    );

// Remove description from metatags tab
$GLOBALS['TCA']['pages']['palettes']['metatags']['showitem'] =
    preg_replace('/description(.*,|.*$)/', '', $GLOBALS['TCA']['pages']['palettes']['metatags']['showitem']);
