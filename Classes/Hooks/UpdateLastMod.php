<?php
namespace YoastSeoForTypo3\YoastSeo\Hooks;

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

use TYPO3\CMS\Core\Database\DatabaseConnection;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class UpdateLastMod
 * @package YoastSeoForTypo3\YoastSeo\Hooks
 */
class UpdateLastMod
{
    /**
     * @param $status
     * @param $table
     * @param $uid
     * @param array $fieldArray
     * @param $pObj
     */
    public function processDatamap_afterDatabaseOperations($status, $table, $uid, array $fieldArray, $pObj)
    {
        if ($table === 'pages') {
            if (GeneralUtility::isFirstPartOfStr($uid, 'NEW')) {
                $uid = $pObj->substNEWwithIDs[$uid];
            }

            if (!empty($uid)) {
                $this->getDatabaseConnection()->exec_UPDATEquery(
                    'pages',
                    'uid=' . (int)$uid,
                    [
                        'last_mod' => time()
                    ]
                );
            }
        } elseif ($table === 'tt_content') {
            if (GeneralUtility::isFirstPartOfStr($uid, 'NEW')) {
                $uid = $pObj->substNEWwithIDs[$uid];
            }

            // get page of content element
            $row = $this->getDatabaseConnection()->exec_SELECTgetSingleRow(
                'pid',
                'tt_content',
                'uid=' . (int)$uid
            );

            if (!empty($row['pid'])) {
                $this->getDatabaseConnection()->exec_UPDATEquery(
                    'pages',
                    'uid=' . (int)$row['pid'],
                    [
                        'last_mod' => time()
                    ]
                );
            }
        }
    }


    /**
     * @return DatabaseConnection
     */
    protected function getDatabaseConnection()
    {
        return $GLOBALS['TYPO3_DB'];
    }
}
