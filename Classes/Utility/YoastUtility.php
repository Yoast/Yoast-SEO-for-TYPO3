<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Utility;

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

class YoastUtility
{
    protected const COLUMN_NAME_FOCUSKEYWORD = 'tx_yoastseo_focuskeyword';

    /**
     * @param array<string, mixed>|null $configuration
     * @return int[]
     */
    public static function getAllowedDoktypes(?array $configuration = null): array
    {
        $allowedDoktypes = array_map(function ($doktype) {
            return (int)$doktype;
        }, array_values((array)($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['yoast_seo']['allowedDoktypes'] ?? [])));

        if (isset($configuration['allowedDoktypes'])
            && is_array($configuration['allowedDoktypes'])
        ) {
            foreach ($configuration['allowedDoktypes'] as $doktype) {
                if (!in_array($doktype, $allowedDoktypes)) {
                    $allowedDoktypes[] = (int)$doktype;
                }
            }
        }

        return $allowedDoktypes ?: [1];
    }

    /**
     * @param array<string, mixed>|null $configuration
     */
    public static function getAllowedDoktypesList(?array $configuration = null): string
    {
        return implode(',', self::getAllowedDoktypes($configuration));
    }

    /**
     * @param array<string, mixed> $pageRecord
     * @param array<string, mixed> $pageTs
     */
    public static function snippetPreviewEnabled(int $pageId, array $pageRecord, ?array $pageTs = null): bool
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

        return !$pageRecord['tx_yoastseo_hide_snippet_preview'];
    }

    public static function getFocusKeywordOfRecord(int $uid, string $table = 'pages'): ?string
    {
        $focusKeyword = '';
        if (empty((int)$uid)) {
            return '';
        }

        $record = BackendUtility::getRecord($table, $uid);
        if (\is_array($record) && array_key_exists(self::COLUMN_NAME_FOCUSKEYWORD, $record)) {
            $focusKeyword = $record[self::COLUMN_NAME_FOCUSKEYWORD];
        }
        return $focusKeyword;
    }

    /**
     * @return array<string, array{keyword: string, synonyms: string}>
     */
    public static function getRelatedKeyphrases(string $parentTable, int $parentId): array
    {
        $config = [];
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable(
            'tx_yoastseo_related_focuskeyword'
        );
        $relatedKeyphrases = $queryBuilder->select('*')
            ->from('tx_yoastseo_related_focuskeyword')
            ->where(
                $queryBuilder->expr()->eq('tablenames', $queryBuilder->createNamedParameter($parentTable)),
                $queryBuilder->expr()->eq('uid_foreign', $parentId)
            )
            ->executeQuery()
            ->fetchAllAssociative();

        foreach ($relatedKeyphrases as $relatedKeyphrase) {
            $config['rk' . (int)$relatedKeyphrase['uid']] = [
                'keyword' => (string)$relatedKeyphrase['keyword'],
                'synonyms' => (string)$relatedKeyphrase['synonyms']
            ];
        }

        return $config;
    }

    /**
     * Returns true if Yoast extension is in production mode. You need a webpack dev server running to load
     * JS files if not in production mode
     *
     * You can set development by using TypoScript "module.tx_yoastseo.settings.developmentMode = 1"
     *
     * @param array<string, mixed>|null $configuration
     * @return bool
     */
    public static function inProductionMode(?array $configuration = null): bool
    {
        if ($configuration === null) {
            $configuration = self::getTypoScriptConfiguration();
        }

        return !((int)($_ENV['YOAST_DEVELOPMENT_MODE'] ?? 0) === 1 || (int)($configuration['developmentMode'] ?? 0) === 1);
    }

    /**
     * @return array<string, mixed>
     */
    protected static function getTypoScriptConfiguration(): array
    {
        $configurationManager = GeneralUtility::makeInstance(ConfigurationManager::class);
        return $configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS,
            'yoastseo'
        );
    }

    /**
     * Fix absolute url when site configuration has '/' as base
     *
     * @param string $url
     * @return string
     */
    public static function fixAbsoluteUrl(string $url): string
    {
        if (str_starts_with($url, '/')) {
            $url = GeneralUtility::getIndpEnv('TYPO3_REQUEST_HOST') . $url;
        }
        return $url;
    }
}
