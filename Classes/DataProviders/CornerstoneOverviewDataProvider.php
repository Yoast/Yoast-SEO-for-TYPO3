<?php
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

/**
 * Class CornerstoneOverviewDataProvider
 */
class CornerstoneOverviewDataProvider extends AbstractOverviewDataProvider
{

    /**
     * @param bool $returnOnlyCount
     * @return int|array
     */
    public function getData($returnOnlyCount = false)
    {
        $language = (int)$this->callerParams['language'];
        $constraints = [];
        $table = 'pages';

        $qb = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);

        if ($language > 0) {
            $constraints[] = $qb->expr()->eq('sys_language_uid', $language);
        }

        $constraints[] = $qb->expr()->eq('tx_yoastseo_cornerstone', 1);

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
