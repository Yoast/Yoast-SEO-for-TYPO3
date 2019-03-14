<?php
namespace YoastSeoForTypo3\YoastSeo\UserFunctions;

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class LinkingSuggestions
{
    public function render(): string
    {
        $json = file_get_contents('php://input');
        $data = json_decode($json);

        $words = $data->words ?? [];

        $qb = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_yoast_seo_premium_prominent_words');
        $qb->getRestrictions()->removeAll();

        $links = [];
        foreach ($words as $word) {
            $combined = implode(' ', $word->_words);

            $records = $qb->select('*')
                ->from('tx_yoast_seo_premium_prominent_words')
                ->where(
                    $qb->expr()->eq('word', $qb->createNamedParameter($combined))
                )
                ->orderBy('occurrences', 'DESC')
                ->execute()
                ->fetchAll();

            foreach ($records as $record) {
                $data = BackendUtility::getRecord($record['table'], $record['pid']);
                $labelField = $GLOBALS['TCA'][$record['table']]['ctrl']['label'];

                $links[$combined][] = ['label' => $data[$labelField], 'uid' => $record['uid'], 'table' => $record['table']];
            }

        }

//        $table = $data->table ?? 'pages';
//        $pageId = (int)$data->pageId;
//        $languageId = (int)$data->languageId;
//
//        $qb->delete('tx_yoast_seo_premium_prominent_words')
//            ->where(
//                $qb->expr()->eq('table', $qb->createNamedParameter($table)),
//                $qb->expr()->eq('pid', $pageId),
//                $qb->expr()->eq('sys_language_uid', $languageId)
//            )
//            ->execute();
//
//        if (is_array($words)) {
//            foreach ($words as $word) {
//                $qb->insert('tx_yoast_seo_premium_prominent_words')
//                    ->values(
//                        [
//                            'pid' => $pageId,
//                            'table' => $table,
//                            'sys_language_uid' => $languageId,
//                            'word' => implode(' ', $word->_words),
//                            'occurrences' => (int)$word->_occurrences
//                        ]
//                    )
//                    ->execute();
//            }
//        }
        return json_encode(['OK', 'links' => $links]);
    }
}
