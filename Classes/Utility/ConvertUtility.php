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

use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Exception;

/**
 * Class ConvertUtility
 * @package YoastSeoForTypo3\YoastSeo\Utility
 */
class ConvertUtility
{
    protected static $fieldsToRename = [
        'tx_yoastseo_title' => 'seo_title',
        'tx_yoastseo_canonical_url' => 'canonical_url'
    ];


    /**
     * @param bool $dryRun
     *
     * @return mixed
     */
    public static function convert($dryRun = false)
    {
        // Rename fields
        foreach (self::$fieldsToRename as $srcField => $dstField) {
            if (self::fieldNeedsUpdate($dstField, $srcField)) {
                if ($dryRun) {
                    return true;
                }

                self::updateRecords($dstField, $srcField);
            }
        }
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
                $field = self::getOldField($table, 'tx_yoastseo_title');
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
    protected static function seoTitleFieldNeedsUpdate()
    {
        $numberOfPageRecords = 0;
        $tables = ['pages', 'pages_language_overlay'];

        foreach ($tables as $table) {
            try {
                $field = self::getOldField($table, 'tx_yoastseo_title');
                $numberOfPageRecords += $GLOBALS['TYPO3_DB']->exec_SELECTcountRows('uid', $table, $field . ' != "" AND seo_title = ""');
            } catch (Exception $e) {
                return false;
            }
        }
        return (bool)$numberOfPageRecords;
    }

    /**
     * @param string $newField
     * @param string $oldField
     * @param array $tables
     * @return bool
     */
    protected static function updateRecords($newField, $oldField, $tables = ['pages', 'pages_language_overlay'])
    {
        /** @var DataHandler $tce */
        $tce = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\DataHandling\\DataHandler');

        try {
            foreach ($tables as $table) {
                $field = self::getOldField($table, $oldField);
                $rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('uid, ' . $field, $table, $field . ' != "" AND ' . $newField . ' = ""');
                foreach ($rows as $row) {
                    $data = [];
                    $data[$table][$row['uid']][$newField] = $row[$field];

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
    protected static function fieldNeedsUpdate($newField, $oldField, $tables = ['pages', 'pages_language_overlay'])
    {
        $numberOfPageRecords = 0;

        foreach ($tables as $table) {
            try {
                $field = self::getOldField($table, $oldField);
                $numberOfPageRecords += $GLOBALS['TYPO3_DB']->exec_SELECTcountRows('uid', $table, $field . ' != "" AND ' . $newField . ' = ""');
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
    protected static function getOldField($table, $field)
    {
        $oldField = '';
        $fieldsToTest = [$field, 'zzz_deleted_' . $field];

        foreach ($fieldsToTest as $fieldToTest) {
            /** @var \mysqli_result $test */
            $test = $GLOBALS['TYPO3_DB']->sql_query('SHOW COLUMNS FROM `'. $table . '` LIKE \'' . $fieldToTest . '\';');
            if ($test->num_rows) {
                $oldField = $fieldToTest;
            }
        }

        if (!$oldField) {
            throw new Exception('No SEO title field found');
        }
        return $oldField;
    }
}
