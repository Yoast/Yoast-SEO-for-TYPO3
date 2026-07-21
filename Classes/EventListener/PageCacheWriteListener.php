<?php

/**
 * This file is part of the "yoast_seo" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\EventListener;

use TYPO3\CMS\Frontend\Event\AfterCacheableContentIsGeneratedEvent;
use YoastSeoForTypo3\YoastSeo\Service\YoastRequestService;

class PageCacheWriteListener
{
    public function __construct(
        protected YoastRequestService $yoastRequestService
    ) {}

    public function __invoke(AfterCacheableContentIsGeneratedEvent $event): void
    {
        if ($this->yoastRequestService->isValidRequest($event->getRequest()->getServerParams())) {
            // A validated Yoast request renders with hidden pages/records visible
            // (PageRequestMiddleware forces VisibilityAspect(true)) and reuses the
            // anonymous page-cache identifier. Persisting it would poison the shared
            // page cache, so never write the analysis render to the page cache.
            $event->disableCaching();
        }
    }
}
