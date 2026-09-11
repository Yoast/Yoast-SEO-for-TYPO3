<?php

/**
 * This file is part of the "yoast_seo" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Tests\Unit\Middleware;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Http\Response;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Cache\CacheInstruction;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use YoastSeoForTypo3\YoastSeo\Middleware\PageRequestMiddleware;
use YoastSeoForTypo3\YoastSeo\Service\YoastRequestService;

#[CoversClass(PageRequestMiddleware::class)]
class PageRequestMiddlewareTest extends UnitTestCase
{
    protected bool $resetSingletonInstances = true;
    protected Context $context;
    protected YoastRequestService&MockObject $yoastRequestService;
    protected RequestHandlerInterface&MockObject $handler;
    protected ?ServerRequestInterface $handledRequest = null;
    protected PageRequestMiddleware $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->context = new Context();
        GeneralUtility::setSingletonInstance(Context::class, $this->context);

        $this->handler = $this->createMock(RequestHandlerInterface::class);
        $this->handler->method('handle')->willReturnCallback(function (ServerRequestInterface $request): Response {
            $this->handledRequest = $request;
            return new Response();
        });

        $this->yoastRequestService = $this->createMock(YoastRequestService::class);
        $this->subject = new PageRequestMiddleware($this->yoastRequestService);
    }

    #[Test]
    public function regularRequestIsPassedThroughUntouched(): void
    {
        $this->yoastRequestService->method('isValidRequest')->willReturn(false);
        $request = new ServerRequest('https://example.com/');

        $this->subject->process($request, $this->handler);

        self::assertSame($request, $this->handledRequest);
        self::assertFalse($this->context->getAspect('visibility')->includeHiddenPages());
    }

    #[Test]
    public function yoastRequestIncludesHiddenPagesAndDisablesCaching(): void
    {
        $this->yoastRequestService->method('isValidRequest')->willReturn(true);

        $this->subject->process(new ServerRequest('https://example.com/'), $this->handler);

        self::assertTrue($this->context->getAspect('visibility')->includeHiddenPages());
        self::assertFalse($this->context->getAspect('visibility')->includeHiddenContent());
        if (!class_exists(CacheInstruction::class)) {
            self::assertTrue($this->handledRequest->getAttribute('noCache'));
            return;
        }
        self::assertFalse($this->handledRequest->getAttribute('frontend.cache.instruction')->isCachingAllowed());
    }

    #[Test]
    public function yoastRequestDisablesCachingOnExistingCacheInstruction(): void
    {
        if (!class_exists(CacheInstruction::class)) {
            self::markTestSkipped('CacheInstruction does not exist in TYPO3 v12');
        }
        $this->yoastRequestService->method('isValidRequest')->willReturn(true);
        $cacheInstruction = new CacheInstruction();
        $request = (new ServerRequest('https://example.com/'))
            ->withAttribute('frontend.cache.instruction', $cacheInstruction);

        $this->subject->process($request, $this->handler);

        self::assertSame($cacheInstruction, $this->handledRequest->getAttribute('frontend.cache.instruction'));
        self::assertFalse($cacheInstruction->isCachingAllowed());
    }
}
