<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Widgets\Provider;

interface PageProviderInterface
{
    public function getPages(): array;
}
