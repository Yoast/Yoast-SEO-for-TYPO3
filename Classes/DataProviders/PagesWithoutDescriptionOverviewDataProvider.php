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

use YoastSeoForTypo3\YoastSeo\Utility\YoastUtility;

/**
 * Class PagesWithoutDescriptionOverviewDataProvider
 * @package YoastSeoForTypo3\YoastSeo\DataProviders
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
        $language = (int)$this->callerParams['language'];

        $fields = '*';
        $where = '(description = "" OR description IS NULL) AND doktype IN (' . $doktypes . ') AND tx_yoastseo_dont_use=0 AND tx_yoastseo_hide_snippet_preview=0 AND deleted=0';
        $table = 'pages';
        $order = 'title';

        if ($language > 0) {
            $fields = 'l.*';
            $table = 'pages_language_overlay l inner join pages p on l.pid = p.uid';
            $where = '(l.description = "" OR l.description IS NULL) AND l.doktype IN (' . $doktypes . ') AND p.tx_yoastseo_dont_use=0 AND p.tx_yoastseo_hide_snippet_preview=0 AND p.deleted=0';
            $where .= ' AND l.deleted = 0 AND l.sys_language_uid = ' . $language;
            $order = 'l.title';
        }

        if ($returnOnlyCount === false) {
            return $this->getDatabaseConnection()->exec_SELECTgetRows(
                $fields,
                $table,
                $where,
                '',
                $order
            );
        }

        return $this->getDatabaseConnection()->exec_SELECTcountRows(
            '*',
            $table,
            $where
        );
    }
}
