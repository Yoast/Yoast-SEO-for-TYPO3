<?php

/**
 * This file is part of the "yoast_seo" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

return [
    'dependencies' => ['core', 'backend', 'frontend_request'],
    'imports' => [
        '@yoast/yoast-seo-for-typo3/' => 'EXT:yoast_seo/Resources/Public/JavaScript/',
    ],
];
