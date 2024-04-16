<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Record;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\Exception\MissingArrayPathException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use YoastSeoForTypo3\YoastSeo\Service\DbalService;

class RecordService implements SingletonInterface
{
    protected bool $recordsChecked = false;

    protected ?Record $activeRecord = null;

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
     * @param \YoastSeoForTypo3\YoastSeo\Record\Record[] $records
     * @return \YoastSeoForTypo3\YoastSeo\Record\Record|null
     */
    protected function findRecord(array $records): ?Record
    {
        foreach ($records as $record) {
            if (empty($record->getGetParameters())) {
                continue;
            }

            foreach ($record->getGetParameters() as $getParameters) {
                try {
                    $getValue = ArrayUtility::getValueByPath($_GET, implode('/', $getParameters));
                } catch (MissingArrayPathException $e) {
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

    protected function getRecordData(Record $record): array
    {
        $statement = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable($record->getTableName())
            ->select(['*'], $record->getTableName(), ['uid' => $record->getRecordUid()]);

        $recordRow = GeneralUtility::makeInstance(DbalService::class)->getSingleResult($statement);

        return (array)GeneralUtility::makeInstance(PageRepository::class)
            ->getLanguageOverlay($record->getTableName(), (array)$recordRow);
    }
}
