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
        $where = 'tx_yoastseo_cornerstone = 1';

        $language = (int)$this->callerParams['language'];
        $table = 'pages';
        if ($language > 0) {
            $table = 'pages_language_overlay';
            $where .= ' AND sys_language_uid = ' . $language;
        }

        if ($returnOnlyCount === false) {
            return $this->getDatabaseConnection()->exec_SELECTgetRows(
                '*',
                $table,
                $where,
                '',
                'title'
            );
        }

        return $this->getDatabaseConnection()->exec_SELECTcountRows(
            '*',
            $table,
            $where
        );
    }
}
