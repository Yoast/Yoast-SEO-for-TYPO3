<?php

/**
 * This file is part of the "yoast_seo" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

\YoastSeoForTypo3\YoastSeo\Utility\RecordUtility::configureForRecord('tx_sitepackage_domain_model_minimal')
    ->setGetParameters([
        ['tx_sitepackage_minimal', 'minimal'],
    ])
    ->setSitemapFields(true)
    ->setAddDescriptionField(true)
    ->setFieldsPosition('after:sys_language_uid');
