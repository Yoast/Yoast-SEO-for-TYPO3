<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\EventListener;

use TYPO3\CMS\Core\Database\Event\AlterTableDefinitionStatementsEvent;

class TableDefinitionsListener extends AbstractBuilderListener
{
    public function __invoke(AlterTableDefinitionStatementsEvent $event): void
    {
        foreach ($this->getRecordsFromRegistry() as $record) {
            $this->builder
                ->setRecord($record)
                ->build();
        }
        $event->addSqlData(implode(LF . LF, $this->builder->getResult()));
    }
}
