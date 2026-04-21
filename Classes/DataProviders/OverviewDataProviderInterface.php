<?php

/**
 * This file is part of the "yoast_seo" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\DataProviders;

use Doctrine\DBAL\Result;
use YoastSeoForTypo3\YoastSeo\Service\Overview\Dto\DataProviderRequest;

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
