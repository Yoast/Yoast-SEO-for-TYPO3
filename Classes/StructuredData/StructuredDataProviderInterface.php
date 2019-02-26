<?php
declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\StructuredData;

interface StructuredDataProviderInterface
{
    /**
     * @return array
     */
    public function getData(): array;
}
