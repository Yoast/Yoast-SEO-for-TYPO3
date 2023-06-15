<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\EventListener;

use TYPO3\CMS\Core\Configuration\Event\AfterTcaCompilationEvent;
use YoastSeoForTypo3\YoastSeo\Record\RecordRegistry;

class TcaBuiltListener extends AbstractListener
{
    public function addRecordTca(AfterTcaCompilationEvent $event): void
    {
        $GLOBALS['TCA'] = $event->getTca();
        foreach (RecordRegistry::getInstance()->getRecords() as $record) {
            $this->builder
                ->setRecord($record)
                ->build();
        }
        $event->setTca($GLOBALS['TCA']);
    }
}
