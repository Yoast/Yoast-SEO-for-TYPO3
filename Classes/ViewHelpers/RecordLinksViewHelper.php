<?php
namespace YoastSeoForTypo3\YoastSeo\ViewHelpers;

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

use TYPO3\CMS\Backend\Utility\BackendUtility;

class RecordLinksViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper
{

    /**
     * @param string $table
     * @param string $command
     * @param int $uid
     * @param string $module
     * @return string
     */
    public function render($table, $command, $uid, $module)
    {
        $returnUrl = BackendUtility::getModuleUrl(
            $module,
            $_GET
        );

        switch ($command) {
            case 'edit':
                $urlParameters = [
                    'edit' => [
                        $table => [
                            $uid => $command
                        ]
                    ],
                    'returnUrl' => $returnUrl
                ];
                $module = 'record_edit';
                break;
            case 'delete':
                $urlParameters = [
                    'cmd[' . $table . '][' . $uid . '][delete]' => 1,
                    'redirect' => $returnUrl,
                ];
                $module = 'tce_db';
                break;
        }

        return BackendUtility::getModuleUrl($module, $urlParameters);
    }
}
