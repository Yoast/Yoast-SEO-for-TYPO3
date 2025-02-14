<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Service\Preview\ContentExtractors;

interface BaseUrlParserInterface
{
    public function getBaseUrl(mixed $urlParts): string;
}
