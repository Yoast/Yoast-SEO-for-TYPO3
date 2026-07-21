<?php

/**
 * This file is part of the "yoast_seo" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Context\VisibilityAspect;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use YoastSeoForTypo3\YoastSeo\Service\YoastRequestService;

final readonly class PageRequestMiddleware implements MiddlewareInterface
{
    public function __construct(
        protected YoastRequestService $yoastRequestService,
    ) {}

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (!$this->yoastRequestService->isValidRequest($request->getServerParams())) {
            return $handler->handle($request);
        }

        $context = GeneralUtility::makeInstance(Context::class);
        $context->setAspect('visibility', new VisibilityAspect(true));

        // Reading from the page cache is prevented by PageCacheListener and writing by
        // PageCacheWriteListener, so the analysis always reflects the current content and
        // never persists a "show hidden" render into the shared page cache.
        return $handler->handle($request);
    }
}
