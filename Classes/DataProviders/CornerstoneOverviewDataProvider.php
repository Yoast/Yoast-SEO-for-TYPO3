<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\DataProviders;

use Doctrine\DBAL\Driver\ResultStatement;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class CornerstoneOverviewDataProvider
 */
class CornerstoneOverviewDataProvider extends AbstractOverviewDataProvider
{
    /**
     * @param bool $returnOnlyCount
     * @return int|array
     */
    public function getData(bool $returnOnlyCount = false)
    {
        return $this->getRestrictedPagesResults($returnOnlyCount);
    }

    protected function getResults(array $pageIds = []): ?ResultStatement
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable(self::PAGES_TABLE);

        $constraints = [
            $queryBuilder->expr()->eq('sys_language_uid', (int)$this->callerParams['language']),
            $queryBuilder->expr()->eq('tx_yoastseo_cornerstone', 1)
        ];

        if (count($pageIds) > 0) {
            $constraints[] = $queryBuilder->expr()->in(
                (int)$this->callerParams['language'] > 0 ? $GLOBALS['TCA']['pages']['ctrl']['transOrigPointerField'] : 'uid',
                $pageIds
            );
        }

        return $queryBuilder->select('*')
            ->from(self::PAGES_TABLE)
            ->where(...$constraints)
            ->execute();
    }
}
