<?php

/**
 * This file is part of the "yoast_seo" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Hooks;

use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use YoastSeoForTypo3\YoastSeo\Constants\TableNames;

#[Autoconfigure(public: true)]
class DisableAnalysisCleanupHook
{
    public function __construct(
        protected ConnectionPool $connectionPool,
    ) {}

    /**
     * @param array<string, mixed> $fieldArray
     */
    public function processDatamap_postProcessFieldArray(
        string $status,
        string $table,
        string|int $id,
        array &$fieldArray,
        DataHandler $dataHandler,
    ): void {
        if ($table !== TableNames::PAGES) {
            return;
        }

        if (!isset($fieldArray['tx_yoastseo_disable_analysis'])) {
            return;
        }

        if ((int)$fieldArray['tx_yoastseo_disable_analysis'] !== 1) {
            return;
        }

        $uid = (int)$id;

        $fieldArray['tx_yoastseo_score_readability'] = '';
        $fieldArray['tx_yoastseo_score_seo'] = '';
        $fieldArray['tx_yoastseo_cornerstone'] = 0;

        $this->deleteProminentWords($uid, $table);
    }

    protected function deleteProminentWords(int $uid, string $table): void
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable(TableNames::PROMINENT_WORD);
        $queryBuilder->delete(TableNames::PROMINENT_WORD)
            ->where(
                $queryBuilder->expr()->eq('uid_foreign', $uid),
                $queryBuilder->expr()->eq('tablenames', $queryBuilder->createNamedParameter($table)),
            )
            ->executeStatement();
    }
}
