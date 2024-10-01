<?php

use YoastSeoForTypo3\YoastSeo\Middleware\PageRequestMiddleware;

return [
    'frontend' => [
        'yoast-seo-page-request' => [
            'target' => PageRequestMiddleware::class,
            'before' => [
                'typo3/cms-frontend/tsfe',
            ],
            'after' => [
                'typo3/cms-frontend/eid',
            ],
        ],
    ],
];
