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
        if ($table !== TableNames::PAGES || $status !== 'update') {
            return;
        }

        if (!isset($fieldArray['tx_yoastseo_disable_analysis'])) {
            return;
        }

        if ((int)$fieldArray['tx_yoastseo_disable_analysis'] !== 1) {
            return;
        }

        $fieldArray['tx_yoastseo_score_readability'] = '';
        $fieldArray['tx_yoastseo_score_seo'] = '';
        $fieldArray['tx_yoastseo_cornerstone'] = 0;

        $this->deleteProminentWords((int)$id);
    }

    /**
     * Prominent words of a translation are stored with the default language uid as uid_foreign.
     */
    protected function deleteProminentWords(int $uid): void
    {
        $languageField = $GLOBALS['TCA'][TableNames::PAGES]['ctrl']['languageField'];
        $transOrigPointerField = $GLOBALS['TCA'][TableNames::PAGES]['ctrl']['transOrigPointerField'];
        $page = $this->connectionPool->getConnectionForTable(TableNames::PAGES)
            ->select([$languageField, $transOrigPointerField], TableNames::PAGES, ['uid' => $uid])
            ->fetchAssociative();
        if ($page === false) {
            return;
        }

        $languageId = (int)$page[$languageField];
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable(TableNames::PROMINENT_WORD);
        $queryBuilder->delete(TableNames::PROMINENT_WORD)
            ->where(
                $queryBuilder->expr()->eq('uid_foreign', $languageId > 0 ? (int)$page[$transOrigPointerField] : $uid),
                $queryBuilder->expr()->eq('sys_language_uid', $languageId),
                $queryBuilder->expr()->eq('tablenames', $queryBuilder->createNamedParameter(TableNames::PAGES)),
            )
            ->executeStatement();
    }
}
