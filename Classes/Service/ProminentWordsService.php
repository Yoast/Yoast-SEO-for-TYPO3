<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Service;

use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Exception\SiteNotFoundException;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ProminentWordsService
{
    protected const PROMINENT_WORDS_TABLE = 'tx_yoastseo_prominent_word';

    protected int $uid;
    protected int $pid;
    protected string $table;
    protected int $languageId;

    public function __construct(
        protected ConnectionPool $connectionPool
    ) {}

    /**
     * @param array<string, int|string> $prominentWords
     */
    public function saveProminentWords(
        int $uid,
        ?int $pid,
        string $table,
        int $languageId,
        array $prominentWords
    ): void {
        $this->uid = $uid;
        $this->pid = $pid ?? $this->getPidForRecord($uid, $table);
        $this->table = $table;
        $this->languageId = $languageId;

        $wordsToDelete = [];

        $oldWords = $this->getOldWords();
        foreach ($oldWords as $oldWord) {
            if (!isset($prominentWords[$oldWord['stem']])) {
                $wordsToDelete[] = $oldWord['uid'];
                continue;
            }

            $this->updateIfWeightHasChanged($oldWord, (int)$prominentWords[$oldWord['stem']]);
            unset($prominentWords[$oldWord['stem']]);
        }

        $this->deleteProminentWords($wordsToDelete);
        $this->createNewWords($prominentWords);
    }

    /**
     * @param array{uid: int, weight: int} $oldWord
     */
    protected function updateIfWeightHasChanged(array $oldWord, int $weight): void
    {
        if ((int)$oldWord['weight'] === $weight) {
            return;
        }
        $queryBuilder = $this->getQueryBuilder();
        $queryBuilder->update(self::PROMINENT_WORDS_TABLE)
            ->set('weight', $weight)
            ->where(
                $queryBuilder->expr()->eq('uid', $oldWord['uid'])
            )
            ->setMaxResults(1)
            ->executeStatement();
    }

    /**
     * @param int[] $wordsToDelete
     */
    protected function deleteProminentWords(array $wordsToDelete): void
    {
        if ($wordsToDelete === []) {
            return;
        }

        $queryBuilder = $this->getQueryBuilder();
        $queryBuilder->delete(self::PROMINENT_WORDS_TABLE)
            ->where(
                $queryBuilder->expr()->in(
                    'uid',
                    $queryBuilder->createNamedParameter($wordsToDelete, Connection::PARAM_INT_ARRAY)
                )
            )
            ->executeStatement();
    }

    /**
     * @param array<string, int|string> $prominentWords
     */
    protected function createNewWords(array $prominentWords): void
    {
        $site = $this->getSiteRootPageId();
        foreach ($prominentWords as $word => $weight) {
            $data = [
                'pid' => $this->table === 'pages' ? $this->uid : $this->pid,
                'sys_language_uid' => $this->languageId,
                'uid_foreign' => $this->uid,
                'site' => $site,
                'tablenames' => $this->table,
                'stem' => $word,
                'weight' => (int)$weight
            ];
            $this->connectionPool->getConnectionForTable(self::PROMINENT_WORDS_TABLE)
                ->insert(self::PROMINENT_WORDS_TABLE, $data);
        }
    }

    /**
     * @return array<array{uid: int, stem: string, weight: int}>
     */
    protected function getOldWords(): array
    {
        $queryBuilder = $this->getQueryBuilder();
        $queryBuilder->select('uid', 'stem', 'weight')
            ->from(self::PROMINENT_WORDS_TABLE)
            ->where(
                $queryBuilder->expr()->eq('uid_foreign', $this->uid),
                $queryBuilder->expr()->eq('tablenames', $queryBuilder->createNamedParameter($this->table)),
                $queryBuilder->expr()->eq('sys_language_uid', $this->languageId)
            );
        /** @var array<array{uid: int, stem: string, weight: int}> $oldWords */
        $oldWords = $queryBuilder->executeQuery()->fetchAllAssociative();
        return $oldWords;
    }

    protected function getPidForRecord(int $uid, string $table): int
    {
        $connection = $this->connectionPool->getConnectionForTable($table);
        $record = $connection->select(['pid'], $table, ['uid' => $uid])->fetchAssociative();
        return (int)($record['pid'] ?? 0);
    }

    protected function getSiteRootPageId(): int
    {
        try {
            $site = GeneralUtility::makeInstance(SiteFinder::class)->getSiteByPageId(
                $this->table === 'pages' ? $this->uid : $this->pid
            );
            return $site->getRootPageId();
        } catch (SiteNotFoundException $e) {
            return 0;
        }
    }

    protected function getQueryBuilder(): QueryBuilder
    {
        return $this->connectionPool->getQueryBuilderForTable(self::PROMINENT_WORDS_TABLE);
    }
}
