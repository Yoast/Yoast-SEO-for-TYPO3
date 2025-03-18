<?php

/**
 * This file is part of the "yoast_seo" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\EventListener;

use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Frontend\Event\BeforePageCacheIdentifierIsHashedEvent;
use YoastSeoForTypo3\YoastSeo\Service\Frontend\FrontendServiceInterface;

#[Autoconfigure(public: true)]
final readonly class PageCacheIdentifierListener
{
    public function __construct(
        #[Autowire(service: 'cache.runtime')]
        private FrontendInterface $runtimeCache,
    ) {}

    public function __invoke(BeforePageCacheIdentifierIsHashedEvent $event): void
    {
        $pageCacheIdentifierParameters = $event->getPageCacheIdentifierParameters();
        $this->runtimeCache->set(
            FrontendServiceInterface::CACHE_IDENTIFIER,
            $pageCacheIdentifierParameters['id'] . '_' . hash('xxh3', serialize($pageCacheIdentifierParameters))
        );
    }
}
