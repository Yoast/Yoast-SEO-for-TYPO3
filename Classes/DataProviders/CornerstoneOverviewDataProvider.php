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
 * @package YoastSeoForTypo3\YoastSeo\DataProviders
 */
class CornerstoneOverviewDataProvider extends AbstractOverviewDataProvider
{

    /**
     * @param bool $returnOnlyCount
     * @return int|array
     */
    public function getData($returnOnlyCount = false)
    {
        $qb = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('pages');

        $constraints = [
            $qb->expr()->eq('tx_yoastseo_cornerstone', 1),
            $qb->expr()->eq('sys_language_uid', (int)$this->callerParams['language'])
        ];

        $query = $qb->select('*')
            ->from('pages')
            ->where(...$constraints)
            ->execute();

        if ($returnOnlyCount === false) {
            return $query->fetchAll();
        }

        return $query->rowCount();
    }
}
