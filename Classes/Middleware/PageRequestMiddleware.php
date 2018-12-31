<?php
namespace YoastSeoForTypo3\YoastSeo\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Context\VisibilityAspect;
use TYPO3\CMS\Core\Utility\GeneralUtility;

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
        $serverParams = $request->getServerParams();
        if (isset($serverParams['HTTP_X_YOAST_PAGE_REQUEST'])
            && $serverParams['HTTP_X_YOAST_PAGE_REQUEST'] === $this->getRequestHash()) {
            $context = GeneralUtility::makeInstance(Context::class);
            $context->setAspect('visibility', new VisibilityAspect(true));
        }
        return $handler->handle($request);
    }

    /**
     * Get request hash
     *
     * @return string
     */
    protected function getRequestHash(): string
    {
        return GeneralUtility::hmac(
            GeneralUtility::getIndpEnv('TYPO3_REQUEST_URL')
        );
    }
}
