<?php

/**
 * This file is part of the "yoast_seo" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Service\Ajax;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Http\JsonResponse;
use YoastSeoForTypo3\YoastSeo\Constants\TableNames;
use YoastSeoForTypo3\YoastSeo\Service\ProminentWordsService;

class ProminentWordsHandler extends AbstractAjaxHandler
{
    public function __construct(
        protected ProminentWordsService $prominentWordsService,
        protected ConnectionPool $connectionPool,
    ) {}

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $data = $this->getJsonData($request);

        if (isset($data['words'], $data['uid'])) {
            $table = $data['table'] ?? TableNames::PAGES;
            if ($this->isAnalysisDisabled($table, (int)$data['uid'], (int)($data['languageId'] ?? 0))) {
                return new JsonResponse(['OK']);
            }

            $this->prominentWordsService->saveProminentWords(
                (int)$data['uid'],
                isset($data['pid']) ? (int)$data['pid'] : null,
                $table,
                (int)($data['languageId'] ?? 0),
                (array)$data['words']
            );
        }

        return new JsonResponse(['OK']);
    }

    protected function isAnalysisDisabled(string $table, int $uid, int $languageId): bool
    {
        if ($table !== TableNames::PAGES) {
            return false;
        }

        $queryBuilder = $this->connectionPool->getQueryBuilderForTable(TableNames::PAGES);
        $queryBuilder->getRestrictions()->removeAll()->add(new DeletedRestriction());
        $queryBuilder->select('tx_yoastseo_disable_analysis')->from(TableNames::PAGES);

        if ($languageId > 0) {
            $queryBuilder->where(
                $queryBuilder->expr()->eq(
                    $GLOBALS['TCA'][TableNames::PAGES]['ctrl']['transOrigPointerField'],
                    $uid
                ),
                $queryBuilder->expr()->eq(
                    $GLOBALS['TCA'][TableNames::PAGES]['ctrl']['languageField'],
                    $languageId
                )
            );
        } else {
            $queryBuilder->where($queryBuilder->expr()->eq('uid', $uid));
        }

        $record = $queryBuilder->executeQuery()->fetchAssociative();

        return (bool)($record['tx_yoastseo_disable_analysis'] ?? false);
    }
}
