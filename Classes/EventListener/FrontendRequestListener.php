<?php

/**
 * This file is part of the "yoast_seo" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\EventListener;

use MaxServ\FrontendRequest\Event\ModifyRequestEvent;
use Psr\Http\Message\RequestInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use YoastSeoForTypo3\YoastSeo\Service\Hmac\HmacGeneratorServiceInterface;

#[Autoconfigure(public: true)]
class FrontendRequestListener
{
    public function __construct(
        protected HmacGeneratorServiceInterface $hmacService
    ) {}

    public function __invoke(ModifyRequestEvent $event): void
    {
        $request = $this->addBasicAuthenticationHeader($event->getRequest());
        $event->setRequest(
            $request->withHeader('X-Yoast-Page-Request', $this->hmacService->generate(
                $event->getContext()->getUrl()
            ))
        );
    }

    protected function addBasicAuthenticationHeader(RequestInterface $request): RequestInterface
    {
        if (!isset($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['yoast_seo']['previewSettings']['basicAuth'])) {
            return $request;
        }

        $basicAuth = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['yoast_seo']['previewSettings']['basicAuth'];
        if (!is_array($basicAuth) || empty($basicAuth['username'] ?? '') || empty($basicAuth['password'] ?? '')) {
            return $request;
        }

        return $request->withHeader(
            'Authorization',
            'Basic ' . base64_encode(
                $basicAuth['username'] . ':' . $basicAuth['password']
            )
        );
    }
}
