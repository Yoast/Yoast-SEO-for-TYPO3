<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Widgets\Provider;

interface PageProviderInterface
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function getPages(): array;
}
