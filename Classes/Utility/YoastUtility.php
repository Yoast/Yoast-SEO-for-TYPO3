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
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class YoastUtility
 */
class YoastUtility
{
    /**
     * @var string
     */
    const COLUMN_NAME_FOCUSKEYWORD = 'tx_yoastseo_focuskeyword';

    /**
     * @param array $configuration
     * @param bool $returnInString
     *
     * @return array
     */
    public static function getAllowedDoktypes($configuration = null, $returnInString = false)
    {
        // @phpstan-ignore-next-line
        $allowedDoktypes = array_map(function ($doktype) {
            return (int)$doktype;
        }, array_values((array)$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['yoast_seo']['allowedDoktypes']));

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

        $allowedDoktypes = $allowedDoktypes ?: [1];

        if ($returnInString) {
            return implode(',', $allowedDoktypes);
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
        $showPreview = !$pageRecord['tx_yoastseo_hide_snippet_preview'];

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

    /**
     * @param int $uid
     * @param string $table
     *
     * @return string
     */
    public static function getFocusKeywordOfPage($uid, $table = 'pages')
    {
        $focusKeyword = '';
        if (empty((int)$uid)) {
            return '';
        }

        $record = CMS\Backend\Utility\BackendUtility::getRecord($table, $uid);
        if (\is_array($record) && array_key_exists(self::COLUMN_NAME_FOCUSKEYWORD, $record)) {
            $focusKeyword = $record[self::COLUMN_NAME_FOCUSKEYWORD];
        }

        $params = [
            'keyword' => $focusKeyword,
            'table' => $table,
            'uid' => $uid
        ];

        if ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['yoast_seo']['get_focus_keyword']) {
            foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['yoast_seo']['get_focus_keyword'] as $_funcRef) {
                if ($_funcRef) {
                    $tmp = new \stdClass();
                    CMS\Core\Utility\GeneralUtility::callUserFunction($_funcRef, $params, $tmp);
                }
            }
        }

        return $params['keyword'];
    }

    /**
     * @param string $parentTable
     * @param int $parentId
     * @return array
     */
    public static function getRelatedKeyphrases($parentTable, $parentId): array
    {
        $config = [];
        $queryBuilder = CMS\Core\Utility\GeneralUtility::makeInstance(CMS\Core\Database\ConnectionPool::class)->getQueryBuilderForTable('tx_yoast_seo_premium_focus_keywords');
        if ($queryBuilder) {
            $relatedKeyphrases = $queryBuilder->select('*')
                ->from('tx_yoast_seo_premium_focus_keywords')
                ->where(
                    $queryBuilder->expr()->eq('parenttable', $queryBuilder->createNamedParameter($parentTable)),
                    $queryBuilder->expr()->eq('parentid', (int)$parentId)
                )
                ->execute()
                ->fetchAll();

            foreach ($relatedKeyphrases as $relatedKeyphrase) {
                $config['rk' . (int)$relatedKeyphrase['uid']] = [
                    'keyword' => (string)$relatedKeyphrase['keyword'],
                    'synonyms' => (string)$relatedKeyphrase['synonyms']
                ];
            }
        }

        return $config;
    }

    /**
     * @return bool
     */
    public static function isPremiumInstalled()
    {
        return (bool)CMS\Core\Utility\ExtensionManagementUtility::isLoaded('yoast_seo_premium');
    }

    /**
     * Returns true if Yoast extension is in production mode. You need a webpack dev server running to load
     * JS files if not in production mode
     *
     * You can set development by using TypoScript "module.tx_yoastseo.settings.developmentMode = 1"
     *
     * @param array|null $configuration
     * @return bool
     */
    public static function inProductionMode($configuration = null)
    {
        if ($configuration === null) {
            $configuration = self::getTypoScriptConfiguration();
        }

        if ((int)$_ENV['YOAST_DEVELOPMENT_MODE'] === 1 || (int)$configuration['developmentMode'] === 1) {
            return false;
        }

        return true;
    }

    protected static function getTypoScriptConfiguration()
    {
        /** @var CMS\Extbase\Object\ObjectManager $objectManager */
        $objectManager = CMS\Core\Utility\GeneralUtility::makeInstance(CMS\Extbase\Object\ObjectManager::class);
        /** @var CMS\Extbase\Configuration\ConfigurationManager $configurationManager */
        $configurationManager = $objectManager->get(CMS\Extbase\Configuration\ConfigurationManager::class);
        $configuration = $configurationManager->getConfiguration(
            CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS,
            'yoastseo'
        );

        return $configuration;
    }
    /**
     * @param string $utm_term
     * @param string $utm_content
     * @param string $utm_source
     * @return string
     * @throws CMS\Core\Package\Exception
     */
    public static function getYoastLink($utm_term = 'Go premium', $utm_content = '', $utm_source = 'yoast-seo-for-typo3')
    {
        preg_match('/^(\d+\.\d+\.\d+).*/', phpversion(), $php_version);
        $parameters = [
            'utm_source' => $utm_source,
            'utm_medium' => 'software',
            'utm_term' => $utm_term,
            'utm_content' => $utm_content,
            'utm_campaign' => 'typo3-ad',
            'php_version' => $php_version[1] ?: 'unknown',
            'platform' => 'TYPO3',
            'platform_version' => CMS\Core\Utility\VersionNumberUtility::getNumericTypo3Version(),
            'software' => self::isPremiumInstalled() ? 'premium' : 'free',
            'software_version' => CMS\Core\Utility\ExtensionManagementUtility::getExtensionVersion('yoast_seo'),
            'role' => ''
        ];

        return 'https://yoast.com/typo3-extensions-seo/?' . http_build_query($parameters);
    }

    /**
     * Fix absolute url when site configuration has '/' as base
     *
     * @param string $url
     * @return string
     */
    public static function fixAbsoluteUrl(string $url): string
    {
        if (strpos($url, '/') === 0) {
            $url = GeneralUtility::getIndpEnv('TYPO3_REQUEST_HOST') . $url;
        }
        return $url;
    }
}
