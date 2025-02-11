<?php

\YoastSeoForTypo3\YoastSeo\Utility\RecordUtility::configureForRecord('tx_news_domain_model_news')
    ->setGetParameters([
        ['tx_news_pi1', 'news'],
        ['tx_news_pi1', 'news_preview']
    ])
    ->setSitemapFields(false)
    ->setFieldsPosition('after:bodytext');
