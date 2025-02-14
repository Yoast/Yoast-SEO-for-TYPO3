<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Service\Preview\ContentExtractors;

interface BodyProcessorInterface
{
    public function getBody(string $content): string;
}
