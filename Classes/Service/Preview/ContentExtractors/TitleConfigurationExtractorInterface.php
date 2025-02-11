<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Service\Preview\ContentExtractors;

interface TitleConfigurationExtractorInterface
{
    /**
     * @return array{titlePrepend: string, titleAppend: string}
     */
    public function getTitleConfiguration(string $content): array;
}
