<?php

/**
 * This file is part of the "yoast_seo" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use YoastSeoForTypo3\YoastSeo\Middleware\PageRequestMiddleware;

return [
    'frontend' => [
        'yoast-seo-page-request' => [
            'target' => PageRequestMiddleware::class,
            'before' => [
                'typo3/cms-frontend/page-resolver',
            ],
            'after' => [
                'typo3/cms-frontend/eid',
            ],
        ],
    ],
];
