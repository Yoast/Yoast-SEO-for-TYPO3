<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Frontend;

use TYPO3\CMS\Frontend\Event\AfterCacheableContentIsGeneratedEvent;
use YoastSeoForTypo3\YoastSeo\Service\YoastRequestService;

class AfterCacheableContentIsGeneratedListener
{
    public function __construct(
        protected YoastRequestService $yoastRequestService
    ) {}

    public function __invoke(AfterCacheableContentIsGeneratedEvent $event): void
    {
        $serverParams = $GLOBALS['TYPO3_REQUEST'] ? $GLOBALS['TYPO3_REQUEST']->getServerParams() : $_SERVER;
        if ($this->yoastRequestService->isValidRequest($serverParams)) {
            $event->disableCaching();
        }
    }
}
