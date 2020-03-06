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
 * @package YoastSeoForTypo3\YoastSeo\Middleware
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
        if (YoastRequestHash::isValid($request)) {
            $context = GeneralUtility::makeInstance(Context::class);
            $context->setAspect('visibility', new VisibilityAspect(true));

            if (isset($request->getServerParams()['HTTP_ORIGIN'])) {
                $response = $handler->handle($request);
                $response = $response->withHeader('Access-Control-Allow-Origin', $request->getServerParams()['HTTP_ORIGIN']);

                return $response;
            }
        }

        return $handler->handle($request);
    }
}
