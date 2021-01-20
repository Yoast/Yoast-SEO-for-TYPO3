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

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use YoastSeoForTypo3\YoastSeo\Utility\YoastUtility;

/**
 * Class PagesWithoutDescriptionOverviewDataProvider
 */
class PagesWithoutDescriptionOverviewDataProvider extends AbstractOverviewDataProvider
{

    /**
     * @param bool $returnOnlyCount
     * @return int|array
     */
    public function getData($returnOnlyCount = false)
    {
        $doktypes = implode(',', YoastUtility::getAllowedDoktypes()) ?: '-1';
        $table = 'pages';

        $qb = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);

        $constraints = [
            $qb->expr()->orX(
                $qb->expr()->eq('description', $qb->createNamedParameter('')),
                $qb->expr()->isNull('description')
            ),
            $qb->expr()->in('doktype', $doktypes),
            $qb->expr()->eq('tx_yoastseo_hide_snippet_preview', 0),
            $qb->expr()->eq('sys_language_uid', (int)$this->callerParams['language'])
        ];

        $query = $qb->select('*')
            ->from($table)
            ->where(...$constraints)
            ->execute();

        if ($returnOnlyCount === false) {
            return $query->fetchAll();
        }

        return $query->rowCount();
    }
}
