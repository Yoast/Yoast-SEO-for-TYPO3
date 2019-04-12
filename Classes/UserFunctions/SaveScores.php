<?php
namespace YoastSeoForTypo3\YoastSeo\UserFunctions;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class SaveScores
{
    public function render(): string
    {
        $json = file_get_contents('php://input');
        $data = json_decode($json);

        if (!empty($data->table) && !empty($data->uid)) {
            $qb = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($data->table);

            $row = $qb->select('*')
                ->from($data->table)
                ->where(
                    $qb->expr()->eq('uid', $qb->createNamedParameter($data->uid, \PDO::PARAM_INT))
                )
                ->execute()
                ->fetchAll();

            if (!empty($row[0])
                && array_key_exists('tx_yoastseo_score_readability', $row[0])
                && array_key_exists('tx_yoastseo_score_seo', $row[0])
            ) {
                $qb->update($data->table)
                    ->where(
                        $qb->expr()->eq('uid', $qb->createNamedParameter($data->uid, \PDO::PARAM_INT))
                    )
                    ->set('tx_yoastseo_score_readability', (string)$data->readabilityScore)
                    ->set('tx_yoastseo_score_seo', (string)$data->seoScore)
                    ->execute();
            }
        }
        return json_encode(['OK']);
    }
}
