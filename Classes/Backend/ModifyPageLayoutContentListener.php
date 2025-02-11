<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Backend;

use TYPO3\CMS\Backend\Controller\Event\ModifyPageLayoutContentEvent;

class ModifyPageLayoutContentListener
{
    public function __construct(
        protected PageLayoutHeader $pageLayoutHeader
    ) {}

    public function __invoke(ModifyPageLayoutContentEvent $event): void
    {
        $event->addHeaderContent($this->pageLayoutHeader->render([], $event->getModuleTemplate()));
    }
}
