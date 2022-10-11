<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\DataProviders;

use Doctrine\DBAL\Driver\ResultStatement;

interface OverviewDataProviderInterface
{
    public function getResults(array $pageIds = []): ?ResultStatement;
}
