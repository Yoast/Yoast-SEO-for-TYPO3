<?php

/**
 * This file is part of the "yoast_seo" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use GuzzleHttp\Client;
use YoastSeoForTypo3\Translations\Fetcher\TranslationFetcher;
use YoastSeoForTypo3\Translations\Processor\TranslationProcessor;

require __DIR__ . '/../vendor/autoload.php';

$client = new Client([
    'base_uri' => 'https://translate.wordpress.org/api/projects/wp-plugins/wordpress-seo/dev/',
    'headers' => [
        'Accept' => 'application/json',
        'User-Agent' => 'GuzzleHttp',
    ],
]);

$domain = 'wordpress-seo';
$fetcher = new TranslationFetcher($client);
$processor = new TranslationProcessor($fetcher, $domain);
$processor->processTranslations();
