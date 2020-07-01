<?php
namespace YoastSeoForTypo3\YoastSeo\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Context\VisibilityAspect;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use YoastSeoForTypo3\YoastSeo\Utility\YoastRequestHash;

/**
 * Class PageRequestMiddleware
 */
class PageRequestMiddleware implements MiddlewareInterface
{
    /**
     * Process page request
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Server\RequestHandlerInterface $handler
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (YoastRequestHash::isValid($request->getServerParams())) {
            $context = GeneralUtility::makeInstance(Context::class);
            $context->setAspect('visibility', new VisibilityAspect(true));
        }
        return $handler->handle($request);
    }
}
