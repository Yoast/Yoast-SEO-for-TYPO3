<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\MetaTag\AdvancedRobots;

use TYPO3\CMS\Core\MetaTag\MetaTagManagerInterface;
use TYPO3\CMS\Core\MetaTag\MetaTagManagerRegistry;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class RobotsMetaTagManager
{
    protected MetaTagManagerInterface $manager;

    public function __construct()
    {
        $metaTagManagerRegistry = GeneralUtility::makeInstance(MetaTagManagerRegistry::class);
        $this->manager = $metaTagManagerRegistry->getManagerForProperty('robots');
    }

    public function removeRobotsTag(): void
    {
        $this->manager->removeProperty('robots');
    }

    public function addRobotsTag(string $robots): void
    {
        $this->manager->addProperty('robots', $robots);
    }
}
