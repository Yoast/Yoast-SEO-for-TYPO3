<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Record;

use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use YoastSeoForTypo3\YoastSeo\Exception\UnsupportedVersionException;

class RecordRegistry implements SingletonInterface
{
    /**
     * @var \YoastSeoForTypo3\YoastSeo\Record\Record[]
     */
    protected ?array $records = null;

    public static function getInstance(): RecordRegistry
    {
        return GeneralUtility::makeInstance(__CLASS__);
    }

    public function addRecord(string $tableName): Record
    {
        if (GeneralUtility::makeInstance(Typo3Version::class)->getMajorVersion() < 10) {
            throw new UnsupportedVersionException(
                'The record feature is only available for TYPO3 10 and higher',
                1659438424
            );
        }
        return $this->records[$tableName]
            ?? ($this->records[$tableName] = GeneralUtility::makeInstance(Record::class)->setTableName($tableName));
    }

    public function removeRecord(string $tableName): void
    {
        if (isset($this->records[$tableName])) {
            unset($this->records);
        }
    }

    /**
     * @return \YoastSeoForTypo3\YoastSeo\Record\Record[]
     */
    public function getRecords(): array
    {
        if ($this->records === null) {
            $this->retrieveRecords();
        }
        return $this->records;
    }

    protected function retrieveRecords(): void
    {
        $cache = GeneralUtility::makeInstance(CacheManager::class)->getCache('yoastseo_recordcache');
        if (($recordsFromCache = $cache->get('records')) !== false) {
            $this->records = $recordsFromCache;
            return;
        }

        $this->buildRecordsFromTca();
        $cache->set('records', $this->records);
    }

    protected function buildRecordsFromTca(): void
    {
        $this->records = [];
        foreach ($GLOBALS['TCA'] as $table => $tca) {
            if (!isset($tca['yoast_seo'])) {
                continue;
            }

            $yoastSeoConfiguration = $tca['yoast_seo'];

            $this->addRecord($table)
                ->setDefaultSeoFields((bool)($yoastSeoConfiguration['defaultSeoFields'] ?? true))
                ->setYoastSeoFields((bool)($yoastSeoConfiguration['yoastFields'] ?? true))
                ->setDescriptionField((string)($yoastSeoConfiguration['descriptionField'] ?? 'description'))
                ->setAddDescriptionField((bool)($yoastSeoConfiguration['addDescriptionField'] ?? false))
                ->setGetParameters((array)($yoastSeoConfiguration['getParameters'] ?? []));
        }
    }
}
