<?php

return [
    'frontend' => [
        'yoast-seo-page-request' => [
            'target' => \YoastSeoForTypo3\YoastSeo\Middleware\PageRequestMiddleware::class,
            'before' => [
                'typo3/cms-frontend/tsfe'
            ],
            'after' => [
                'typo3/cms-frontend/eid'
            ]
        ]
    ]
];
