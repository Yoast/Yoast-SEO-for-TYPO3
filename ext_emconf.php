<?php

/**
 * This file is part of the "yoast_seo" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

$EM_CONF[$_EXTKEY] = [
    'title' => 'Yoast SEO for TYPO3',
    'description' => 'Optimise your website for search engines with Yoast SEO for TYPO3. With this extension you get all the technical SEO stuff you need and will help editors to write high quality content.',
    'category' => 'misc',
    'author' => 'MaxServ / Yoast',
    'author_company' => 'MaxServ B.V., Yoast',
    'author_email' => '',
    'clearCacheOnLoad' => 0,
    'state' => 'stable',
    'uploadfolder' => 0,
    'version' => '11.1.0',
    'constraints' => [
        'depends' => [
            'typo3' => '12.4.0-14.4.99',
            'seo' => '12.4.0-14.4.99',
            'frontend_request' => '1.0.0-1.99.99',
        ],
    ],
    'autoload' => [
        'psr-4' => ['YoastSeoForTypo3\\YoastSeo\\' => 'Classes'],
    ],
];
