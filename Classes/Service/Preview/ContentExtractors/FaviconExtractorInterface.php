<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Service\Preview\ContentExtractors;

interface FaviconExtractorInterface
{
    public function getFaviconSrc(string $baseUrl, string $content): string;
}
