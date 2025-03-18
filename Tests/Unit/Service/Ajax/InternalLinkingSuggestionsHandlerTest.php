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
use YoastSeoForTypo3\YoastSeo\Service\Ajax\InternalLinkingSuggestionsHandler;
use YoastSeoForTypo3\YoastSeo\Service\LinkingSuggestionsService;

#[CoversClass(InternalLinkingSuggestionsHandler::class)]
class InternalLinkingSuggestionsHandlerTest extends UnitTestCase
{
    protected LinkingSuggestionsService $linkingSuggestionsService;
    protected InternalLinkingSuggestionsHandler $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->linkingSuggestionsService = $this->createMock(LinkingSuggestionsService::class);
        $this->subject = new InternalLinkingSuggestionsHandler($this->linkingSuggestionsService);
    }

    protected function createRequestWithJsonBody(array $data): ServerRequest
    {
        $body = new Stream('php://temp', 'rw');
        $body->write(json_encode($data));
        $body->rewind();
        return new ServerRequest(body: $body);
    }

    #[Test]
    public function testHandleDelegatesToService(): void
    {
        $words = [['stem' => 'seo', 'occurrences' => 10]];
        $expectedLinks = [['label' => 'SEO Page', 'id' => 2]];

        $this->linkingSuggestionsService->expects(self::once())
            ->method('getLinkingSuggestions')
            ->with($words, 1, 0, '<p>Some content</p>')
            ->willReturn($expectedLinks);

        $request = $this->createRequestWithJsonBody([
            'words' => $words,
            'excludedPage' => 1,
            'languageId' => 0,
            'content' => '<p>Some content</p>',
        ]);

        $response = $this->subject->handle($request);

        self::assertInstanceOf(JsonResponse::class, $response);
        $body = json_decode((string)$response->getBody(), true);
        self::assertSame($expectedLinks, $body['links']);
        self::assertSame(1, $body['excludedPage']);
        self::assertSame(0, $body['languageId']);
    }

    #[Test]
    public function testHandleWithDefaultValues(): void
    {
        $this->linkingSuggestionsService->expects(self::once())
            ->method('getLinkingSuggestions')
            ->with([], 0, 0, '')
            ->willReturn([]);

        $request = $this->createRequestWithJsonBody([]);

        $response = $this->subject->handle($request);

        self::assertInstanceOf(JsonResponse::class, $response);
        $body = json_decode((string)$response->getBody(), true);
        self::assertSame([], $body['links']);
        self::assertSame(0, $body['excludedPage']);
        self::assertSame(0, $body['languageId']);
    }
}
