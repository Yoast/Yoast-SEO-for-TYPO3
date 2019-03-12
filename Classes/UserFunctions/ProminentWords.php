<?php
namespace YoastSeoForTypo3\YoastSeo\UserFunctions;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ProminentWords
{
    public function render(): string
    {
        $json = file_get_contents('php://input');
        $data = json_decode($json);

        $words = $data->words ?? [];
        $table = $data->table ?? 'pages';
        $pageId = (int)$data->pageId;
        $languageId = (int)$data->languageId;

        $qb = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_yoast_seo_premium_prominent_words');
        $qb->getRestrictions()->removeAll();
        $qb->delete('tx_yoast_seo_premium_prominent_words')
            ->where(
                $qb->expr()->eq('table', $qb->createNamedParameter($table)),
                $qb->expr()->eq('pid', $pageId),
                $qb->expr()->eq('sys_language_uid', $languageId)
            )
            ->execute();

        if (is_array($words)) {
            foreach ($words as $word) {
                $qb->insert('tx_yoast_seo_premium_prominent_words')
                    ->values(
                        [
                            'pid' => $pageId,
                            'table' => $table,
                            'sys_language_uid' => $languageId,
                            'word' => implode(' ', $word->_words),
                            'occurrences' => (int)$word->_occurrences
                        ]
                    )
                    ->execute();
            }
        }
        return json_encode(['OK']);
    }
}
