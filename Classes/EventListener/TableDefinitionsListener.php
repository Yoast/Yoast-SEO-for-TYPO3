<?php

/**
 * This file is part of the "yoast_seo" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

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
