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

use TYPO3\CMS;

/**
 * Class YoastUtility
 * @package YoastSeoForTypo3\YoastSeo\Utility
 */
class YoastUtility
{

    /**
     * @param array $configuration
     *
     * @return array
     */
    public static function getAllowedDoktypes($configuration = null)
    {
        // By default only add normal pages
        $allowedDoktypes = [1];

        if ($configuration === null) {
            /** @var CMS\Extbase\Object\ObjectManager $objectManager */
            $objectManager = CMS\Core\Utility\GeneralUtility::makeInstance(CMS\Extbase\Object\ObjectManager::class);
            /** @var CMS\Extbase\Configuration\ConfigurationManager $configurationManager */
            $configurationManager = $objectManager->get(CMS\Extbase\Configuration\ConfigurationManager::class);
            $configuration = $configurationManager->getConfiguration(CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS, 'yoastseo');
        }

        if (is_array($configuration) &&
            array_key_exists('allowedDoktypes', $configuration) &&
            is_array($configuration['allowedDoktypes'])
        ) {
            foreach ($configuration['allowedDoktypes'] as $doktype) {
                if (!in_array($doktype, $allowedDoktypes)) {
                    $allowedDoktypes[] = (int)$doktype;
                }
            }
        }

        return $allowedDoktypes;
    }

    /**
     * @param $pageId
     * @param array $pageRecord
     * @param array $pageTs
     *
     * @return bool
     */
    public static function snippetPreviewEnabled($pageId, array $pageRecord, $pageTs = null)
    {
        $showPreview = !$pageRecord['tx_yoastseo_dont_use'];

        if ($pageTs === null) {
            $pageTs = CMS\Backend\Utility\BackendUtility::getPagesTSconfig($pageId);
        }

        if (is_array($pageTs) &&
            array_key_exists('mod.', $pageTs) &&
            is_array($pageTs['mod.']) &&
            array_key_exists('web_SeoPlugin.', $pageTs['mod.']) &&
            is_array($pageTs['mod.']['web_SeoPlugin.']) &&
            array_key_exists('disableSnippetPreview', $pageTs['mod.']['web_SeoPlugin.']) &&
            (int)$pageTs['mod.']['web_SeoPlugin.']['disableSnippetPreview'] === 1
        ) {
            $showPreview = false;
        }

        return $showPreview;
    }
}
