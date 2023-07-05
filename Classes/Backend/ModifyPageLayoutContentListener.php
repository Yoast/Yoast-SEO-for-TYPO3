<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Backend;

use TYPO3\CMS\Backend\Controller\Event\ModifyPageLayoutContentEvent;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ModifyPageLayoutContentListener
{
    public function __invoke(ModifyPageLayoutContentEvent $event): void
    {
        $pageLayoutHeader = GeneralUtility::makeInstance(PageLayoutHeader::class);
        $event->addHeaderContent($pageLayoutHeader->render([], $event->getModuleTemplate()));
    }
}
