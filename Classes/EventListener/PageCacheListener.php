<?php

/**
 * This file is part of the "yoast_seo" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\EventListener;

use TYPO3\CMS\Frontend\Event\ShouldUseCachedPageDataIfAvailableEvent;
use YoastSeoForTypo3\YoastSeo\Service\YoastRequestService;

class PageCacheListener
{
    public function __construct(
        protected YoastRequestService $yoastRequestService
    ) {}

    public function __invoke(ShouldUseCachedPageDataIfAvailableEvent $event): void
    {
        if ($this->yoastRequestService->isValidRequest($event->getRequest()->getServerParams())) {
            $event->setShouldUseCachedPageData(false);
        }
    }
}
