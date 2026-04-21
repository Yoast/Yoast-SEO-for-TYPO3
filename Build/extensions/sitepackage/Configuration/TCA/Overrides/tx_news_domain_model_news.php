<?php

/**
 * This file is part of the "yoast_seo" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

\YoastSeoForTypo3\YoastSeo\Utility\RecordUtility::configureForRecord('tx_news_domain_model_news')
    ->setGetParameters([
        ['tx_news_pi1', 'news'],
        ['tx_news_pi1', 'news_preview'],
    ])
    ->setSitemapFields(false)
    ->setFieldsPosition('after:bodytext');
