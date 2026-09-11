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
use TYPO3\CMS\Frontend\Cache\CacheInstruction;
use YoastSeoForTypo3\YoastSeo\Service\YoastRequestService;

final readonly class PageRequestMiddleware implements MiddlewareInterface
{
    public function __construct(
        private YoastRequestService $yoastRequestService,
    ) {}

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (!$this->yoastRequestService->isValidRequest($request->getServerParams())) {
            return $handler->handle($request);
        }

        GeneralUtility::makeInstance(Context::class)->setAspect('visibility', new VisibilityAspect(true));

        return $handler->handle($this->disableCaching($request));
    }

    /**
     * Hidden pages are rendered for this request, so nothing may be read from or stored in any cache
     */
    private function disableCaching(ServerRequestInterface $request): ServerRequestInterface
    {
        if (!class_exists(CacheInstruction::class)) {
            return $request->withAttribute('noCache', true); // TYPO3 v12
        }

        $cacheInstruction = $request->getAttribute('frontend.cache.instruction', new CacheInstruction());
        $cacheInstruction->disableCache('EXT:yoast_seo: Yoast analysis request renders hidden pages');

        return $request->withAttribute('frontend.cache.instruction', $cacheInstruction);
    }
}
