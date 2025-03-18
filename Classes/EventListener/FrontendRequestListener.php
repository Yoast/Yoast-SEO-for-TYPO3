<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\EventListener;

use MaxServ\FrontendRequest\Event\ModifyRequestEvent;
use Psr\Http\Message\RequestInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class FrontendRequestListener
{
    public function __invoke(ModifyRequestEvent $event): void
    {
        $request = $this->addBasicAuthenticationHeader($event->getRequest());
        $event->setRequest(
            $request->withHeader('X-Yoast-Page-Request', GeneralUtility::hmac(
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
