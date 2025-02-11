<?php

declare(strict_types=1);

use TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider;

return [
    'extension-yoast' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:yoast_seo/Resources/Public/Icons/Extension.svg',
    ],
    'module-yoast' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:yoast_seo/Resources/Public/Images/Yoast-module-container.svg',
    ],
    'module-yoast-dashboard' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:yoast_seo/Resources/Public/Images/Yoast-module-dashboard.svg',
    ],
    'module-yoast-overview' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:yoast_seo/Resources/Public/Images/Yoast-module-overview.svg',
    ],
    'module-yoast-crawler' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:yoast_seo/Resources/Public/Images/Yoast-module-crawler.svg',
    ],
];
