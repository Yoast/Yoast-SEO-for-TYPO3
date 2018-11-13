<?php
namespace YoastSeoForTypo3\YoastSeo\Hooks;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use YoastSeoForTypo3\YoastSeo\UserFunc\SitemapXml;

/**
 * Class RealUrlAutoConfiguration
 * @package YoastSeoForTypo3\YoastSeo\Hooks
 */
class RealUrlAutoConfiguration
{
    public function addSitemapConfiguration($params)
    {
        return array_merge_recursive(
            $params['config'],
            [
                'fileName' => [
                    'index' => [
                        'sitemap-pages.xml' => [
                            'keyValues' => [
                                'type' => SitemapXml::DOKTYPE,
                                'tx_yoastseo_sitemap' => 'pages'
                            ]
                        ],
                        'sitemap.xml' => [
                            'keyValues' => [
                                'type' => SitemapXml::DOKTYPE
                            ]
                        ]
                    ]
                ]
            ]
        );
    }
}
