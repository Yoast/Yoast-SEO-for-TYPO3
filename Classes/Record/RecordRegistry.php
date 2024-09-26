<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Record;

use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class RecordRegistry implements SingletonInterface
{
    /**
     * @var Record[]
     */
    protected ?array $records = null;

    public static function getInstance(): self
    {
        return GeneralUtility::makeInstance(__CLASS__);
    }

    public function addRecord(string $tableName): Record
    {
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
     * @return Record[]
     */
    public function getRecords(bool $useCache = false): array
    {
        if ($this->records === null) {
            $this->retrieveRecords($useCache);
        }
        return (array)$this->records;
    }

    protected function retrieveRecords(bool $useCache): void
    {
        if ($useCache === false) {
            $this->buildRecordsFromTca();
            return;
        }

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
                ->setSitemapFields((bool)($yoastSeoConfiguration['sitemapFields'] ?? true))
                ->setTitleField((string)($yoastSeoConfiguration['titleField'] ?? 'title'))
                ->setDescriptionField((string)($yoastSeoConfiguration['descriptionField'] ?? 'description'))
                ->setAddDescriptionField((bool)($yoastSeoConfiguration['addDescriptionField'] ?? false))
                ->setGetParameters((array)($yoastSeoConfiguration['getParameters'] ?? []))
                ->setGeneratePageTitle((bool)($yoastSeoConfiguration['generatePageTitle'] ?? true))
                ->setGenerateMetaTags((bool)($yoastSeoConfiguration['generateMetaTags'] ?? true))
                ->setGenerateRobotsTag((bool)($yoastSeoConfiguration['generateRobotsTag'] ?? true));
        }
    }
}
