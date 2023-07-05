<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\DataProviders;

use Doctrine\DBAL\Result;

interface OverviewDataProviderInterface
{
    public function getResults(array $pageIds = []): ?Result;
}
