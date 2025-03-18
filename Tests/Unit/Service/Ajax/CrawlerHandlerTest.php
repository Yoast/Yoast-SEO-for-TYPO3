<?php

/**
 * This file is part of the "yoast_seo" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Tests\Unit\Service\Ajax;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Error\Http\BadRequestException;
use TYPO3\CMS\Core\Http\JsonResponse;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Http\Stream;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use YoastSeoForTypo3\YoastSeo\Service\Ajax\CrawlerHandler;
use YoastSeoForTypo3\YoastSeo\Service\Crawler\CrawlerService;

#[CoversClass(CrawlerHandler::class)]
class CrawlerHandlerTest extends UnitTestCase
{
    protected CrawlerService $crawlerService;
    protected CrawlerHandler $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->crawlerService = $this->createMock(CrawlerService::class);
        $this->subject = new CrawlerHandler($this->crawlerService);
    }

    protected function createRequestWithJsonBody(array $data, string $handlerRequest): ServerRequest
    {
        $body = new Stream('php://temp', 'rw');
        $body->write(json_encode($data));
        $body->rewind();
        return (new ServerRequest(body: $body))->withAttribute('handlerRequest', $handlerRequest);
    }

    #[Test]
    public function testDetermineWithPagesFound(): void
    {
        $this->crawlerService->method('getAmountOfPages')->with(1, 0)->willReturn(42);

        $request = $this->createRequestWithJsonBody(['site' => 1, 'language' => 0], 'determine');
        $response = $this->subject->handle($request);

        self::assertInstanceOf(JsonResponse::class, $response);
        $body = json_decode((string)$response->getBody(), true);
        self::assertSame(42, $body['amount']);
    }

    #[Test]
    public function testDetermineWithNoPagesFound(): void
    {
        $this->crawlerService->method('getAmountOfPages')->with(1, 0)->willReturn(0);

        $request = $this->createRequestWithJsonBody(['site' => 1, 'language' => 0], 'determine');
        $response = $this->subject->handle($request);

        $body = json_decode((string)$response->getBody(), true);
        self::assertArrayHasKey('error', $body);
    }

    #[Test]
    public function testIndexWithPagesRemaining(): void
    {
        $indexInfo = [
            'pages' => [1, 2, 3],
            'current' => 0,
            'nextOffset' => 50,
            'total' => 100,
        ];
        $this->crawlerService->method('getIndexInformation')->with(1, 0, 0)->willReturn($indexInfo);

        $request = $this->createRequestWithJsonBody(['site' => 1, 'language' => 0, 'offset' => 0], 'index');
        $response = $this->subject->handle($request);

        $body = json_decode((string)$response->getBody(), true);
        self::assertSame([1, 2, 3], $body['pages']);
        self::assertSame(100, $body['total']);
    }

    #[Test]
    public function testIndexWithNoMorePages(): void
    {
        $indexInfo = [
            'pages' => [],
            'current' => 100,
            'nextOffset' => 150,
            'total' => 100,
        ];
        $this->crawlerService->method('getIndexInformation')->with(1, 0, 100)->willReturn($indexInfo);

        $request = $this->createRequestWithJsonBody(['site' => 1, 'language' => 0, 'offset' => 100], 'index');
        $response = $this->subject->handle($request);

        $body = json_decode((string)$response->getBody(), true);
        self::assertSame('finished', $body['status']);
        self::assertSame(100, $body['total']);
    }

    #[Test]
    public function testInvalidRequestThrowsBadRequestException(): void
    {
        $body = new Stream('php://temp', 'rw');
        $body->write(json_encode([]));
        $body->rewind();
        $request = (new ServerRequest(body: $body))->withAttribute('handlerRequest', 'invalid');

        $this->expectException(BadRequestException::class);
        $this->subject->handle($request);
    }
}
