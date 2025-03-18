<?php

/**
 * This file is part of the "yoast_seo" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;
use YoastSeoForTypo3\YoastSeo\Service\TcaService;
use YoastSeoForTypo3\YoastSeo\Utility\YoastUtility;

GeneralUtility::makeInstance(TcaService::class)
    ->addYoastFields(
        'pages',
        YoastUtility::getAllowedDoktypesList()
    );

// Remove description from metatags tab
$GLOBALS['TCA']['pages']['palettes']['metatags']['showitem'] =
    preg_replace('/description(.*,|.*$)/', '', $GLOBALS['TCA']['pages']['palettes']['metatags']['showitem']);
