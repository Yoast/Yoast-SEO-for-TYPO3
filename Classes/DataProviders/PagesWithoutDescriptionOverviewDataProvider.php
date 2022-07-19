<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\DataProviders;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use Doctrine\DBAL\Driver\ResultStatement;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use YoastSeoForTypo3\YoastSeo\Utility\YoastUtility;

/**
 * Class PagesWithoutDescriptionOverviewDataProvider
 */
class PagesWithoutDescriptionOverviewDataProvider extends AbstractOverviewDataProvider
{
    protected const PAGES_TABLE = 'pages';

    /**
     * @param bool $returnOnlyCount
     * @return int|array
     */
    public function getData($returnOnlyCount = false)
    {
        return $this->getRestrictedPagesResults($returnOnlyCount);
    }

    protected function getResults(array $pageIds = []): ResultStatement
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable(self::PAGES_TABLE);

        $constraints = [
            $queryBuilder->expr()->orX(
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
            ->execute();
    }
}
