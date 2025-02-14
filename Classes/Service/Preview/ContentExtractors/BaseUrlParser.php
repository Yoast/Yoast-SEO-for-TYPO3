<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Service\Preview\ContentExtractors;

class BaseUrlParser implements BaseUrlParserInterface
{
    public function getBaseUrl(mixed $urlParts): string
    {
        if (!is_array($urlParts)) {
            return '://';
        }
        if ($urlParts['port'] ?? false) {
            return (isset($urlParts['scheme']) ? $urlParts['scheme'] . ':' : '') . '//' . ($urlParts['host'] ?? '') . ':' . $urlParts['port'];
        }
        return (isset($urlParts['scheme']) ? $urlParts['scheme'] . ':' : '') . '//' . ($urlParts['host'] ?? '');
    }
}
