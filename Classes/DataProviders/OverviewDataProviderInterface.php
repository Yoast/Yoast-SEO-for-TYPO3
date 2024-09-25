<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\DataProviders;

use Doctrine\DBAL\Result;
use YoastSeoForTypo3\YoastSeo\Backend\Overview\DataProviderRequest;

interface OverviewDataProviderInterface
{
    public function getKey(): string;

    public function getLabel(): string;

    public function getDescription(): string;

    public function getLink(): ?string;

    public function initialize(DataProviderRequest $dataProviderRequest): void;

    /**
     * @return array<int, array<string, mixed>>
     */
    public function process(): array;

    public function getNumberOfItems(): int;

    /**
     * @param int[] $pageIds
     */
    public function getResults(array $pageIds = []): ?Result;
}
