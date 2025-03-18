<?php

/**
 * This file is part of the "yoast_seo" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Record;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\Exception\MissingArrayPathException;

class RecordService implements SingletonInterface
{
    protected bool $recordsChecked = false;
    protected ?Record $activeRecord = null;

    public function __construct(
        protected readonly ConnectionPool $connectionPool,
        protected readonly PageRepository $pageRepository
    ) {}

    public function getActiveRecord(): ?Record
    {
        if ($this->recordsChecked) {
            return $this->activeRecord;
        }

        $records = RecordRegistry::getInstance()->getRecords(true);
        if (empty($records)) {
            $this->recordsChecked = true;
            return null;
        }

        $this->activeRecord = $this->findRecord($records);
        if ($this->activeRecord instanceof Record) {
            $recordData = $this->getRecordData($this->activeRecord);
            $this->activeRecord->setRecordData($recordData);
        }

        $this->recordsChecked = true;
        return $this->activeRecord;
    }

    /**
     * @param Record[] $records
     * @return Record|null
     */
    protected function findRecord(array $records): ?Record
    {
        $currentGetParameters = $_GET;

        foreach ($records as $record) {
            if (empty($record->getGetParameters())) {
                continue;
            }

            foreach ($record->getGetParameters() as $getParameters) {
                try {
                    $getValue = ArrayUtility::getValueByPath($currentGetParameters, implode('/', $getParameters));
                } catch (MissingArrayPathException) {
                    $getValue = null;
                }
                if ($getValue !== null) {
                    $record->setRecordUid((int)$getValue);
                    return $record;
                }
            }
        }

        return null;
    }

    /**
     * @return array<string, mixed>
     */
    protected function getRecordData(Record $record): array
    {
        $recordRow = $this->connectionPool
            ->getConnectionForTable($record->getTableName())
            ->select(['*'], $record->getTableName(), ['uid' => $record->getRecordUid()])
            ->fetchAssociative();

        return (array)$this->pageRepository->getLanguageOverlay($record->getTableName(), (array)$recordRow);
    }
}
