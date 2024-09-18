<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\DataProviders;

use Doctrine\DBAL\Result;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use YoastSeoForTypo3\YoastSeo\Utility\YoastUtility;

class PagesWithoutDescriptionOverviewDataProvider extends AbstractOverviewDataProvider
{
    protected const PAGES_TABLE = 'pages';

    public function getResults(array $pageIds = []): ?Result
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable(self::PAGES_TABLE);

        $constraints = [
            $queryBuilder->expr()->or(
                $queryBuilder->expr()->eq('description', $queryBuilder->createNamedParameter('')),
                $queryBuilder->expr()->isNull('description')
            ),
            $queryBuilder->expr()->in('doktype', YoastUtility::getAllowedDoktypes()),
            $queryBuilder->expr()->eq('tx_yoastseo_hide_snippet_preview', 0),
            $queryBuilder->expr()->eq('sys_language_uid', (int)$this->callerParams['language'])
        ];

        if (count($pageIds) > 0) {
            $constraints[] = $queryBuilder->expr()->in(
                (int)$this->callerParams['language'] > 0 ? $GLOBALS['TCA']['pages']['ctrl']['transOrigPointerField'] : 'uid',
                $pageIds
            );
        }

        return $queryBuilder->select(...self::PAGES_FIELDS)
            ->from(self::PAGES_TABLE)
            ->where(...$constraints)
            ->executeQuery();
    }
}
