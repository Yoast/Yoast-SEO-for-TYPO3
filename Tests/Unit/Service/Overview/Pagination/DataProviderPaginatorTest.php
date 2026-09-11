<?php

/**
 * This file is part of the "yoast_seo" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Tests\Unit\Service\Overview\Pagination;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use YoastSeoForTypo3\YoastSeo\DataProviders\OverviewDataProviderInterface;
use YoastSeoForTypo3\YoastSeo\Service\Overview\Pagination\DataProviderPaginator;

#[CoversClass(DataProviderPaginator::class)]
class DataProviderPaginatorTest extends UnitTestCase
{
    #[Test]
    public function requestsOnlyTheCurrentPageFromTheDataProvider(): void
    {
        $dataProvider = $this->createMock(OverviewDataProviderInterface::class);
        $dataProvider->method('getNumberOfItems')->willReturn(45);
        $dataProvider->expects(self::once())
            ->method('process')
            ->with(20, 10)
            ->willReturn([['uid' => 21], ['uid' => 22]]);

        $paginator = new DataProviderPaginator($dataProvider, 3, 10);

        self::assertSame([['uid' => 21], ['uid' => 22]], $paginator->getPaginatedItems());
    }

    #[Test]
    public function derivesNumberOfPagesFromTheTotalCountWithoutFetchingEverything(): void
    {
        $dataProvider = $this->createMock(OverviewDataProviderInterface::class);
        $dataProvider->method('getNumberOfItems')->willReturn(45);
        $dataProvider->method('process')->willReturn([]);

        $paginator = new DataProviderPaginator($dataProvider, 1, 10);

        self::assertSame(5, $paginator->getNumberOfPages());
        self::assertSame(1, $paginator->getCurrentPageNumber());
    }

    #[Test]
    public function clampsCurrentPageToTheLastAvailablePage(): void
    {
        $dataProvider = $this->createMock(OverviewDataProviderInterface::class);
        $dataProvider->method('getNumberOfItems')->willReturn(15);
        $dataProvider->method('process')->willReturn([['uid' => 11]]);

        $paginator = new DataProviderPaginator($dataProvider, 99, 10);

        self::assertSame(2, $paginator->getNumberOfPages());
        self::assertSame(2, $paginator->getCurrentPageNumber());
    }

    #[Test]
    public function reportsAtLeastOnePageWhenThereAreNoItems(): void
    {
        $dataProvider = $this->createMock(OverviewDataProviderInterface::class);
        $dataProvider->method('getNumberOfItems')->willReturn(0);
        $dataProvider->method('process')->willReturn([]);

        $paginator = new DataProviderPaginator($dataProvider, 1, 10);

        self::assertSame(1, $paginator->getNumberOfPages());
        self::assertSame([], $paginator->getPaginatedItems());
    }
}
