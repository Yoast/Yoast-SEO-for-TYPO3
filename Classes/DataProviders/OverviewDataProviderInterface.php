<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\DataProviders;

/**
 * Interface OverviewDataProviderInterface
 */
interface OverviewDataProviderInterface
{
    /**
     * @param bool $returnOnlyCount
     * @return int|array
     */
    public function getData(bool $returnOnlyCount = false);
}
