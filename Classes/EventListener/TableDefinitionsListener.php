<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\EventListener;

use TYPO3\CMS\Core\Database\Event\AlterTableDefinitionStatementsEvent;
use YoastSeoForTypo3\YoastSeo\Record\RecordRegistry;

class TableDefinitionsListener extends AbstractListener
{
    public function addDatabaseSchema(AlterTableDefinitionStatementsEvent $event): void
    {
        foreach (RecordRegistry::getInstance()->getRecords() as $record) {
            $this->builder
                ->setRecord($record)
                ->build();
        }
        $event->addSqlData(implode(LF . LF, $this->builder->getResult()));
    }
}
