<?php
namespace YoastSeoForTypo3\YoastSeo\Utility;

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

use Doctrine\DBAL\Query\QueryBuilder;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Exception;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

/**
 * Class ConvertUtility
 * @package YoastSeoForTypo3\YoastSeo\Utility
 */
class ConvertUtility
{

    /**
     * @return void
     */
    public static function convert()
    {
        if (self::seoTitleFieldCNeedsUpdate()) {
            self::seoTitleUpdate();
        }
    }

    /**
     * @return bool
     */
    public static function checkIfConvertIsNeeded()
    {
        $convertNeeded = false;

        if (self::seoTitleFieldCNeedsUpdate()) {
            $convertNeeded = true;
        }

        return $convertNeeded;
    }

    /**
     * @return bool
     */
    protected static function seoTitleUpdate()
    {
        /** @var DataHandler $tce */
        $tce = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\DataHandling\\DataHandler');

        $tables = ['pages', 'pages_language_overlay'];

        try {
            foreach ($tables as $table) {
                $field = self::getOldTitleField($table);
                $rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('uid, ' . $field, $table, $field . ' != "" AND seo_title = ""');
                foreach ($rows as $row) {
                    $data = [];
                    $data[$table][$row['uid']]['seo_title'] = $row[$field];

                    $tce->start($data, []);
                    $tce->process_datamap();

                    $GLOBALS['TYPO3_DB']->exec_UPDATEquery($table, 'uid=' . $row['uid'], [$field => '']);
                }
            }
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @return bool
     */
    protected static function seoTitleFieldCNeedsUpdate()
    {
        $numberOfPageRecords = 0;
        $tables = ['pages', 'pages_language_overlay'];

        foreach ($tables as $table) {
            try {
                $field = self::getOldTitleField($table);
                $numberOfPageRecords += $GLOBALS['TYPO3_DB']->exec_SELECTcountRows('uid', $table, $field . ' != "" AND seo_title = ""');
            } catch (Exception $e) {
                return false;
            }
        }
        return (bool)$numberOfPageRecords;
    }

    /**
     * @param $table
     * @return mixed|string
     * @throws \TYPO3\CMS\Core\Exception
     */
    protected static function getOldTitleField($table)
    {
        $oldField = '';
        $fieldsToTest = ['tx_yoastseo_title', 'zzz_deleted_tx_yoastseo_title'];

        foreach ($fieldsToTest as $field) {
            /** @var \mysqli_result $test */
            $test = $GLOBALS['TYPO3_DB']->sql_query('SHOW COLUMNS FROM `'. $table . '` LIKE \'' . $field . '\';');
            if ($test->num_rows) {
                $oldField = $field;
            }
        }

        if (!$oldField) {
            throw new Exception('No SEO title field found');
        }
        return $oldField;
    }
}
