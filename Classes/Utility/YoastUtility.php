<?php

/**
 * This file is part of the "yoast_seo" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Utility;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class YoastUtility
{
    /**
     * @param array<string, mixed>|null $configuration
     * @return int[]
     */
    public static function getAllowedDoktypes(?array $configuration = null): array
    {
        $allowedDoktypes = (array)($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['yoast_seo']['allowedDoktypes'] ?? []);
        $allowedDoktypes = array_map('intval', array_values($allowedDoktypes));

        if (is_array($configuration['allowedDoktypes'] ?? null)) {
            foreach ($configuration['allowedDoktypes'] as $doktype) {
                $doktype = (int)$doktype;
                if (!in_array($doktype, $allowedDoktypes, true)) {
                    $allowedDoktypes[] = $doktype;
                }
            }
        }

        return $allowedDoktypes ? array_values(array_unique($allowedDoktypes)) : [1];
    }

    /**
     * @param array<string, mixed>|null $configuration
     */
    public static function getAllowedDoktypesList(?array $configuration = null): string
    {
        return implode(',', self::getAllowedDoktypes($configuration));
    }

    /**
     * @return array<string, array{keyword: string, synonyms: string}>
     */
    public static function getRelatedKeyphrases(string $parentTable, int $parentId): array
    {
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

        $result = [];
        foreach ($relatedKeyphrases as $relatedKeyphrase) {
            $result['rk' . (int)$relatedKeyphrase['uid']] = [
                'keyword' => (string)$relatedKeyphrase['keyword'],
                'synonyms' => (string)$relatedKeyphrase['synonyms'],
            ];
        }

        return $result;
    }
}
