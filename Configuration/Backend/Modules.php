<?php

/**
 * This file is part of the "yoast_seo" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use YoastSeoForTypo3\YoastSeo\Controller\CrawlerController;
use YoastSeoForTypo3\YoastSeo\Controller\DashboardController;
use YoastSeoForTypo3\YoastSeo\Controller\OverviewController;

if (GeneralUtility::makeInstance(Typo3Version::class)->getMajorVersion() === 12) {
    $navigationComponent = '@typo3/backend/page-tree/page-tree-element';
} else {
    $navigationComponent = '@typo3/backend/tree/page-tree-element';
}

return [
    'yoast' => [
        'labels' => 'LLL:EXT:yoast_seo/Resources/Private/Language/BackendModule.xlf',
        'iconIdentifier' => 'module-yoast',
        'position' => ['after' => 'web'],
    ],
    'yoast_YoastSeoDashboard' => [
        'parent' => 'yoast',
        'access' => 'user',
        'path' => '/module/yoast/dashboard',
        'iconIdentifier' => 'module-yoast-dashboard',
        'labels' => 'LLL:EXT:yoast_seo/Resources/Private/Language/BackendModuleDashboard.xlf',
        'extensionName' => 'YoastSeo',
        'controllerActions' => [
            DashboardController::class => ['index'],
        ],
    ],
    'yoast_YoastSeoOverview' => [
        'parent' => 'yoast',
        'access' => 'user',
        'path' => '/module/yoast/overview',
        'iconIdentifier' => 'module-yoast-overview',
        'labels' => 'LLL:EXT:yoast_seo/Resources/Private/Language/BackendModuleOverview.xlf',
        'navigationComponent' => $navigationComponent,
        'extensionName' => 'YoastSeo',
        'controllerActions' => [
            OverviewController::class => ['list'],
        ],
    ],
    'yoast_YoastSeoCrawler' => [
        'parent' => 'yoast',
        'access' => 'user',
        'path' => '/module/yoast/crawler',
        'iconIdentifier' => 'module-yoast-crawler',
        'labels' => 'LLL:EXT:yoast_seo/Resources/Private/Language/BackendModuleCrawler.xlf',
        'extensionName' => 'YoastSeo',
        'controllerActions' => [
            CrawlerController::class => ['index', 'resetProgress'],
        ],
    ],
];
