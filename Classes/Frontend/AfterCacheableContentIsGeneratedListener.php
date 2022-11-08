<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Frontend;

use TYPO3\CMS\Frontend\Event\AfterCacheableContentIsGeneratedEvent;
use YoastSeoForTypo3\YoastSeo\Utility\YoastRequestHash;

class AfterCacheableContentIsGeneratedListener
{
    public function __invoke(AfterCacheableContentIsGeneratedEvent $event): void
    {
        $serverParams = $GLOBALS['TYPO3_REQUEST'] ? $GLOBALS['TYPO3_REQUEST']->getServerParams() : $_SERVER;
        if (YoastRequestHash::isValid($serverParams)) {
            $event->disableCaching();
        }
    }
}
