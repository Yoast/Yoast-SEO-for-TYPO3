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
use TYPO3\CMS\Core\Http\JsonResponse;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Http\Stream;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use YoastSeoForTypo3\YoastSeo\Service\Ajax\SaveScoresHandler;
use YoastSeoForTypo3\YoastSeo\Service\SaveScoresService;

#[CoversClass(SaveScoresHandler::class)]
class SaveScoresHandlerTest extends UnitTestCase
{
    protected SaveScoresService $saveScoresService;
    protected SaveScoresHandler $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->saveScoresService = $this->createMock(SaveScoresService::class);
        $this->subject = new SaveScoresHandler($this->saveScoresService);
    }

    protected function createRequestWithJsonBody(array $data): ServerRequest
    {
        $body = new Stream('php://temp', 'rw');
        $body->write(json_encode($data));
        $body->rewind();
        return new ServerRequest(body: $body);
    }

    #[Test]
    public function testHandleDelegatesToServiceWhenTableAndUidPresent(): void
    {
        $data = ['table' => 'pages', 'uid' => 1, 'readabilityScore' => '7', 'seoScore' => '8'];
        $request = $this->createRequestWithJsonBody($data);

        $this->saveScoresService->expects(self::once())->method('save')->with($data);

        $response = $this->subject->handle($request);

        self::assertInstanceOf(JsonResponse::class, $response);
    }

    #[Test]
    public function testHandleDoesNotCallSaveWhenTableMissing(): void
    {
        $request = $this->createRequestWithJsonBody(['uid' => 1]);

        $this->saveScoresService->expects(self::never())->method('save');

        $response = $this->subject->handle($request);

        self::assertInstanceOf(JsonResponse::class, $response);
    }

    #[Test]
    public function testHandleDoesNotCallSaveWhenUidMissing(): void
    {
        $request = $this->createRequestWithJsonBody(['table' => 'pages']);

        $this->saveScoresService->expects(self::never())->method('save');

        $response = $this->subject->handle($request);

        self::assertInstanceOf(JsonResponse::class, $response);
    }

    #[Test]
    public function testHandleAlwaysReturnsJsonResponse(): void
    {
        $request = $this->createRequestWithJsonBody([]);

        $response = $this->subject->handle($request);

        self::assertInstanceOf(JsonResponse::class, $response);
        self::assertSame(200, $response->getStatusCode());
    }
}
