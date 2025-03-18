<?php

/**
 * This file is part of the "yoast_seo" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\ViewHelpers;

use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class RecordLinksViewHelper extends AbstractViewHelper
{
    public function initializeArguments(): void
    {
        $this->registerArgument('uid', 'int', 'uid of record to be edited', true);
        $this->registerArgument('table', 'string', 'target database table', true);
        $this->registerArgument('command', 'string', '', true);
        $this->registerArgument('module', 'string', '', true);
    }

    public function render(): string
    {
        $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
        $returnUri = $uriBuilder->buildUriFromRoute($this->arguments['module'], $_GET);

        $module = '';
        $urlParameters = [];
        switch ($this->arguments['command']) {
            case 'edit':
                $urlParameters = [
                    'edit' => [
                        $this->arguments['table'] => [
                            $this->arguments['uid'] => $this->arguments['command'],
                        ],
                    ],
                    'returnUrl' => (string)$returnUri,
                ];
                $module = 'record_edit';
                break;
            case 'delete':
                $urlParameters = [
                    'cmd[' . $this->arguments['table'] . '][' . $this->arguments['uid'] . '][delete]' => 1,
                    'redirect' => (string)$returnUri,
                ];
                $module = 'tce_db';
                break;
        }

        return (string)$uriBuilder->buildUriFromRoute($module, $urlParameters);
    }
}
