<?php

use YoastSeoForTypo3\YoastSeo\Controller\CrawlerController;
use YoastSeoForTypo3\YoastSeo\Controller\DashboardController;
use YoastSeoForTypo3\YoastSeo\Controller\OverviewController;

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
        'navigationComponent' => '@typo3/backend/page-tree/page-tree-element',
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
