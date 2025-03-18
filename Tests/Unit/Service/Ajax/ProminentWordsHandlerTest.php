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
use YoastSeoForTypo3\YoastSeo\Service\Ajax\ProminentWordsHandler;
use YoastSeoForTypo3\YoastSeo\Service\ProminentWordsService;

#[CoversClass(ProminentWordsHandler::class)]
class ProminentWordsHandlerTest extends UnitTestCase
{
    protected ProminentWordsService $prominentWordsService;
    protected ProminentWordsHandler $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->prominentWordsService = $this->createMock(ProminentWordsService::class);
        $this->subject = new ProminentWordsHandler($this->prominentWordsService);
    }

    protected function createRequestWithJsonBody(array $data): ServerRequest
    {
        $body = new Stream('php://temp', 'rw');
        $body->write(json_encode($data));
        $body->rewind();
        return new ServerRequest(body: $body);
    }

    #[Test]
    public function testHandleCallsSaveWhenWordsAndUidPresent(): void
    {
        $words = ['seo' => 10, 'yoast' => 5];
        $request = $this->createRequestWithJsonBody([
            'words' => $words,
            'uid' => 1,
            'pid' => 0,
            'table' => 'pages',
            'languageId' => 0,
        ]);

        $this->prominentWordsService->expects(self::once())
            ->method('saveProminentWords')
            ->with(1, 0, 'pages', 0, $words);

        $response = $this->subject->handle($request);

        self::assertInstanceOf(JsonResponse::class, $response);
    }

    #[Test]
    public function testHandleDefaultsToPageTable(): void
    {
        $words = ['test' => 3];
        $request = $this->createRequestWithJsonBody([
            'words' => $words,
            'uid' => 5,
        ]);

        $this->prominentWordsService->expects(self::once())
            ->method('saveProminentWords')
            ->with(5, null, 'pages', 0, $words);

        $this->subject->handle($request);
    }

    #[Test]
    public function testHandleDoesNotSaveWhenWordsMissing(): void
    {
        $request = $this->createRequestWithJsonBody(['uid' => 1]);

        $this->prominentWordsService->expects(self::never())->method('saveProminentWords');

        $response = $this->subject->handle($request);

        self::assertInstanceOf(JsonResponse::class, $response);
    }

    #[Test]
    public function testHandleDoesNotSaveWhenUidMissing(): void
    {
        $request = $this->createRequestWithJsonBody(['words' => ['seo' => 10]]);

        $this->prominentWordsService->expects(self::never())->method('saveProminentWords');

        $response = $this->subject->handle($request);

        self::assertInstanceOf(JsonResponse::class, $response);
    }
}
