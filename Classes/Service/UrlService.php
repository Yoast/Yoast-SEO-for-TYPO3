<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Service;

use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Core\Routing\RouteNotFoundException;
use TYPO3\CMS\Core\SingletonInterface;

class UrlService implements SingletonInterface
{
    public function __construct(
        protected UriBuilder $uriBuilder
    ) {}

    public function getSaveScoresUrl(): string
    {
        try {
            return (string)$this->uriBuilder->buildUriFromRoute('ajax_yoast_save_scores');
        } catch (RouteNotFoundException) {
            return '';
        }
    }

    public function getProminentWordsUrl(): string
    {
        try {
            return (string)$this->uriBuilder->buildUriFromRoute('ajax_yoast_prominent_words');
        } catch (RouteNotFoundException) {
            return '';
        }
    }
}
