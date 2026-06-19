<?php

/**
 * This file is part of the "yoast_seo" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Service;

use Doctrine\DBAL\Exception;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use YoastSeoForTypo3\YoastSeo\Constants\TableNames;

class SaveScoresService
{
    public function __construct(
        protected ConnectionPool $connectionPool
    ) {}

    /**
     * @param array<string, string> $data
     */
    public function save(array $data): void
    {
        try {
            $record = $this->getRecord($data);
        } catch (\Exception) {
            return;
        }

        if ($record === null || !isset($data['readabilityScore'], $data['seoScore'])) {
            return;
        }

        $connection = $this->connectionPool->getConnectionForTable($data['table']);
        if (!$this->columnsExist($connection, $data['table'])) {
            return;
        }
        $connection->update($data['table'], [
            'tx_yoastseo_score_readability' => (string)$data['readabilityScore'],
            'tx_yoastseo_score_seo' => (string)$data['seoScore'],
        ], ['uid' => (int)$record['uid']]);
    }

    /**
     * @param array<string, string> $data
     * @return array<string, int|string>|null
     * @throws Exception
     */
    protected function getRecord(array $data): ?array
    {
        if ($data['table'] === TableNames::PAGES) {
            return $this->getPageRecord((int)$data['uid'], (int)($data['languageId'] ?? 0));
        }

        $connection = $this->connectionPool->getConnectionForTable($data['table']);
        $record = $connection->select(
            ['uid'],
            $data['table'],
            ['uid' => (int)$data['uid']],
        )->fetchAssociative();
        return $record === false ? null : $record;
    }

    /**
     * @return array<string, int|string>|null
     * @throws Exception
     */
    protected function getPageRecord(int $pageUid, int $languageId): ?array
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable(TableNames::PAGES);
        $queryBuilder->getRestrictions()->removeAll()->add(new DeletedRestriction());
        $queryBuilder->select('uid', 'tx_yoastseo_disable_analysis')->from(TableNames::PAGES);
        if ($languageId > 0) {
            $queryBuilder->where(
                $queryBuilder->expr()->and(
                    $queryBuilder->expr()->eq(
                        $GLOBALS['TCA'][TableNames::PAGES]['ctrl']['transOrigPointerField'],
                        $queryBuilder->createNamedParameter($pageUid, Connection::PARAM_INT)
                    ),
                    $queryBuilder->expr()->eq(
                        $GLOBALS['TCA'][TableNames::PAGES]['ctrl']['languageField'],
                        $queryBuilder->createNamedParameter($languageId, Connection::PARAM_INT)
                    ),
                    $queryBuilder->expr()->eq('tx_yoastseo_disable_analysis', 0),
                )
            );
        } else {
            $queryBuilder->where(
                $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($pageUid, Connection::PARAM_INT)),
                $queryBuilder->expr()->eq('tx_yoastseo_disable_analysis', 0),
            );
        }
        $record = $queryBuilder->executeQuery()->fetchAssociative();
        return $record === false ? null : $record;
    }

    protected function columnsExist(Connection $connection, string $table): bool
    {
        $schemaManager = $connection->createSchemaManager();
        $columns = $schemaManager->listTableColumns($table);
        return isset($columns['tx_yoastseo_score_readability'], $columns['tx_yoastseo_score_seo']);
    }
}
