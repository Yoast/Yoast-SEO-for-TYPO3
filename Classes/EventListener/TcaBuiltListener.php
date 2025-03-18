<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\EventListener;

use TYPO3\CMS\Core\Configuration\Event\AfterTcaCompilationEvent;

class TcaBuiltListener extends AbstractBuilderListener
{
    public function __invoke(AfterTcaCompilationEvent $event): void
    {
        $GLOBALS['TCA'] = $event->getTca();
        foreach ($this->getRecordsFromRegistry() as $record) {
            $this->builder
                ->setRecord($record)
                ->build();
        }
        $event->setTca($GLOBALS['TCA']);
    }
}
