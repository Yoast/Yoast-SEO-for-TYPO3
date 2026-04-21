<?php

/**
 * This file is part of the "yoast_seo" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Tests\Unit\Dto;

use DG\BypassFinals;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use YoastSeoForTypo3\YoastSeo\Dto\RequestData;

#[CoversClass(RequestData::class)]
class RequestDataTest extends UnitTestCase
{
    protected function setUp(): void
    {
        BypassFinals::enable();
        parent::setUp();
    }

    #[Test]
    public function testToArray(): void
    {
        $requestData = new RequestData(42, 1, '&tx_news_pi1[news]=5');

        self::assertSame([
            'pageId' => 42,
            'languageId' => 1,
            'additionalGetVars' => '&tx_news_pi1[news]=5',
        ], $requestData->toArray());
    }

    #[Test]
    public function testToArrayWithDefaults(): void
    {
        $requestData = new RequestData(1, 0);

        self::assertSame([
            'pageId' => 1,
            'languageId' => 0,
            'additionalGetVars' => '',
        ], $requestData->toArray());
    }
}
