<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Service\Ajax;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Http\JsonResponse;

class SaveScoresHandler extends AbstractAjaxHandler
{
    public function __construct(
        protected ConnectionPool $connectionPool
    ) {}

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $data = $this->getJsonData($request);
        if (!empty($data['table']) && !empty($data['uid'])) {
            $this->saveScores($data);
        }
        return new JsonResponse($data);
    }

    /**
     * @param array<string, string> $data
     */
    protected function saveScores(array $data): void
    {
        $connection = $this->connectionPool->getConnectionForTable($data['table']);
        try {
            $row = $connection->select(
                ['*'],
                $data['table'],
                ['uid' => (int)$data['uid']],
                [],
                [],
                1
            )->fetchAssociative();
        } catch (\Throwable) {
            return;
        }

        if ($row !== false && isset($row['tx_yoastseo_score_readability'], $row['tx_yoastseo_score_seo'])) {
            $connection->update($data['table'], [
                'tx_yoastseo_score_readability' => (string)$data['readabilityScore'],
                'tx_yoastseo_score_seo' => (string)$data['seoScore'],
            ], ['uid' => (int)$data['uid']]);
        }
    }
}
