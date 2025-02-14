<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Utility;

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

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
        $allowedDoktypes = array_unique($allowedDoktypes);

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
                'synonyms' => (string)$relatedKeyphrase['synonyms'],
            ];
        }

        return $config;
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
