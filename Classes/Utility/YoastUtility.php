<?php
declare(strict_types=1);
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

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Package\Exception;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Object\ObjectManager;

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
     * @return array|string
     */
    public static function getAllowedDoktypes($configuration = null, $returnInString = false)
    {
        // @phpstan-ignore-next-line
        $allowedDoktypes = array_map(function ($doktype) {
            return (int)$doktype;
        }, array_values((array)$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['yoast_seo']['allowedDoktypes']));

        if (isset($configuration['allowedDoktypes'])
            && is_array($configuration['allowedDoktypes'])
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
     * @param int $pageId
     * @param array $pageRecord
     * @param array $pageTs
     *
     * @return bool
     */
    public static function snippetPreviewEnabled(int $pageId, array $pageRecord, $pageTs = null): bool
    {
        if (!$GLOBALS['BE_USER'] instanceof BackendUserAuthentication ||
            !$GLOBALS['BE_USER']->check('non_exclude_fields', 'pages:tx_yoastseo_snippetpreview')) {
            return false;
        }

        if ((bool)($GLOBALS['BE_USER']->uc['hideYoastInPageModule'] ?? false)) {
            return false;
        }

        if ($pageTs === null) {
            $pageTs = BackendUtility::getPagesTSconfig($pageId);
        }

        if (isset($pageTs['mod.']['web_SeoPlugin.']['disableSnippetPreview'])
            && (int)$pageTs['mod.']['web_SeoPlugin.']['disableSnippetPreview'] === 1
        ) {
            return false;
        }

        return ($pageRecord['tx_yoastseo_hide_snippet_preview']) ? false : true;
    }

    /**
     * @param int $uid
     * @param string $table
     *
     * @return string|null
     */
    public static function getFocusKeywordOfPage(int $uid, $table = 'pages'): ?string
    {
        $focusKeyword = '';
        if (empty((int)$uid)) {
            return '';
        }

        $record = BackendUtility::getRecord($table, $uid);
        if (\is_array($record) && array_key_exists(self::COLUMN_NAME_FOCUSKEYWORD, $record)) {
            $focusKeyword = $record[self::COLUMN_NAME_FOCUSKEYWORD];
        }

        $params = [
            'keyword' => $focusKeyword,
            'table' => $table,
            'uid' => $uid
        ];

        foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['yoast_seo']['get_focus_keyword'] ?? [] as $_funcRef) {
            if ($_funcRef) {
                $tmp = new \stdClass();
                GeneralUtility::callUserFunction($_funcRef, $params, $tmp);
            }
        }

        return $params['keyword'];
    }

    /**
     * @param string $parentTable
     * @param int $parentId
     * @return array
     */
    public static function getRelatedKeyphrases(string $parentTable, int $parentId): array
    {
        $config = [];
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_yoast_seo_premium_focus_keywords');
        $relatedKeyphrases = $queryBuilder->select('*')
            ->from('tx_yoast_seo_premium_focus_keywords')
            ->where(
                $queryBuilder->expr()->eq('parenttable', $queryBuilder->createNamedParameter($parentTable)),
                $queryBuilder->expr()->eq('parentid', $parentId)
            )
            ->execute()
            ->fetchAll();

        foreach ($relatedKeyphrases as $relatedKeyphrase) {
            $config['rk' . (int)$relatedKeyphrase['uid']] = [
                'keyword' => (string)$relatedKeyphrase['keyword'],
                'synonyms' => (string)$relatedKeyphrase['synonyms']
            ];
        }

        return $config;
    }

    /**
     * @return bool
     */
    public static function isPremiumInstalled(): bool
    {
        return (bool)ExtensionManagementUtility::isLoaded('yoast_seo_premium');
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
    public static function inProductionMode($configuration = null): bool
    {
        if ($configuration === null) {
            $configuration = self::getTypoScriptConfiguration();
        }

        return !((int)($_ENV['YOAST_DEVELOPMENT_MODE'] ?? 0) === 1 || (int)($configuration['developmentMode'] ?? 0) === 1);
    }

    protected static function getTypoScriptConfiguration(): array
    {
        /** @var ObjectManager $objectManager */
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        /** @var ConfigurationManager $configurationManager */
        $configurationManager = $objectManager->get(ConfigurationManager::class);
        return $configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS,
            'yoastseo'
        );
    }
    /**
     * @param string $utm_term
     * @param string $utm_content
     * @param string $utm_source
     * @return string
     * @throws Exception
     */
    public static function getYoastLink($utm_term = 'Go premium', $utm_content = '', $utm_source = 'yoast-seo-for-typo3'): string
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
            'platform_version' => VersionNumberUtility::getNumericTypo3Version(),
            'software' => self::isPremiumInstalled() ? 'premium' : 'free',
            'software_version' => ExtensionManagementUtility::getExtensionVersion('yoast_seo'),
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
