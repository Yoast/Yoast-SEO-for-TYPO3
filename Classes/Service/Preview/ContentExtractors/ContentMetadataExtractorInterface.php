<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Service\Preview\ContentExtractors;

interface ContentMetadataExtractorInterface
{
    public function getTitle(string $content): string;
    public function getDescription(string $content): string;
    public function getLocale(string $content): string;
}
