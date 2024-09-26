<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\StructuredData;

interface StructuredDataProviderInterface
{
    /**
     * @return array<array<string, mixed>>
     */
    public function getData(): array;
}
