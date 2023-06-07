<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Service;

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Exception\SiteNotFoundException;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class LinkingSuggestionsService
{
    protected const PROMINENT_WORDS_TABLE = 'tx_yoastseo_prominent_word';

    protected int $excludePageId;
    protected int $site;
    protected int $languageId;

    protected ConnectionPool $connectionPool;
    protected PageRepository $pageRepository;

    public function __construct(
        ConnectionPool $connectionPool,
        PageRepository $pageRepository
    ) {
        $this->connectionPool = $connectionPool;
        $this->pageRepository = $pageRepository;
    }

    public function getLinkingSuggestions(
        array $words,
        int $excludePageId,
        int $languageId,
        string $content
    ): array {
        if ($words === []) {
            return [];
        }
        $this->excludePageId = $excludePageId;
        $this->site = $this->getSiteRootPageId($excludePageId);
        $this->languageId = $languageId;

        $words = array_column($words, '_occurrences', '_stem');

        // Combine stems, weights and DFs from request
        $requestData = $this->composeRequestData($words);

        // Calculate vector length of the request set (needed for score normalization later)
        $requestVectorLength = $this->computeVectorLength($requestData);

        $requestStems = array_keys($requestData);
        $scores = [];
        $batchSize = 1000;
        $page = 1;

        do {
            // Retrieve the words of all records in this batch that share prominent word stems with request
            $candidatesWords = $this->getCandidateWords($requestStems, $batchSize, $page);

            // Transform the prominent words table so that it indexed by record
            $candidatesWordsByRecord = $this->groupWordsByRecord($candidatesWords);

            $batchScoresSize = 0;
            foreach ($candidatesWordsByRecord as $id => $candidateData) {
                $scores[$id] = $this->calculateScoreForIndexable($requestData, $requestVectorLength, $candidateData);
                ++$batchScoresSize;
            }

            // Sort the list of scores and keep only the top of the scores
            $scores = $this->getTopSuggestions($scores);

            ++$page;
        } while ($batchScoresSize === $batchSize);

        // Return the empty list if no suggestions have been found.
        if ($scores === []) {
            return [];
        }

        return $this->linkRecords($scores, $this->getCurrentContentLinks($content));
    }

    protected function composeRequestData(array $requestWords): array
    {
        $requestDocFrequencies = $this->countDocumentFrequencies(array_keys($requestWords));
        $combinedRequestData = [];
        foreach ($requestWords as $stem => $weight) {
            if (!isset($requestDocFrequencies[$stem])) {
                continue;
            }

            $combinedRequestData[$stem] = [
                'weight' => (int)$weight,
                'df' => $requestDocFrequencies[$stem],
            ];
        }
        return $combinedRequestData;
    }

    protected function countDocumentFrequencies(array $stems): array
    {
        if ($stems === []) {
            return [];
        }

        $queryBuilder = $this->connectionPool->getQueryBuilderForTable(self::PROMINENT_WORDS_TABLE);
        $rawDocFrequencies = $queryBuilder->select('stem')
            ->addSelectLiteral('COUNT(stem) AS document_frequency')
            ->from(self::PROMINENT_WORDS_TABLE)
            ->where(
                $queryBuilder->expr()->in(
                    'stem',
                    $queryBuilder->createNamedParameter($stems, Connection::PARAM_STR_ARRAY)
                ),
                $queryBuilder->expr()->eq('sys_language_uid', $this->languageId),
                $queryBuilder->expr()->eq('site', $this->site)
            )
            ->groupBy('stem')
            ->execute()
            ->fetchAllAssociative();

        $stems = array_map(
            static function ($item) {
                return $item['stem'];
            },
            $rawDocFrequencies
        );

        $docFrequencies = array_fill_keys($stems, 0);
        foreach ($rawDocFrequencies as $rawDocFrequency) {
            $docFrequencies[$rawDocFrequency['stem']] = (int)$rawDocFrequency['document_frequency'];
        }
        return $docFrequencies;
    }

    protected function computeVectorLength(array $prominentWords): float
    {
        $sumOfSquares = 0;
        foreach ($prominentWords as $word) {
            $docFrequency = 1;
            if (array_key_exists('df', $word)) {
                $docFrequency = $word['df'];
            }

            $tfIdf = $this->computeTfIdfScore($word['weight'], $docFrequency);
            $sumOfSquares += ($tfIdf ** 2);
        }
        return sqrt($sumOfSquares);
    }

    protected function computeTfIdfScore(int $termFrequency, int $docFrequency)
    {
        $docFrequency = max(1, $docFrequency);
        return $termFrequency * (1 / $docFrequency);
    }

    protected function getCandidateWords(array $stems, int $batchSize, int $page): array
    {
        return $this->findStemsByRecords(
            $this->findRecordsByStems($stems, $batchSize, $page)
        );
    }

    protected function findStemsByRecords(array $records): array
    {
        if ($records === []) {
            return [];
        }

        $prominentWords = $this->getProminentWords($records);
        $prominentStems = array_column($prominentWords, 'stem');

        $queryBuilder = $this->connectionPool->getQueryBuilderForTable(self::PROMINENT_WORDS_TABLE);
        $documentFreqs = $queryBuilder->select('stem')
            ->addSelectLiteral('COUNT(uid) AS count')
            ->from(self::PROMINENT_WORDS_TABLE)
            ->where(
                $queryBuilder->expr()->in(
                    'stem',
                    $queryBuilder->createNamedParameter($prominentStems, Connection::PARAM_STR_ARRAY)
                ),
                $queryBuilder->expr()->eq('site', $this->site),
                $queryBuilder->expr()->eq('sys_language_uid', $this->languageId)
            )
            ->groupBy('stem')
            ->execute()
            ->fetchAllAssociative();

        $stemCounts = [];
        foreach ($documentFreqs as $documentFreq) {
            $stemCounts[$documentFreq['stem']] = $documentFreq['count'];
        }

        foreach ($prominentWords as &$prominentWord) {
            if (!array_key_exists($prominentWord['stem'], $stemCounts)) {
                continue;
            }
            $prominentWord['df'] = (int)$stemCounts[$prominentWord['stem']];
        }
        return $prominentWords;
    }

    protected function findRecordsByStems(array $stems, int $batchSize, int $page): array
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable(self::PROMINENT_WORDS_TABLE);
        return $queryBuilder->select('pid', 'tablenames')
            ->from(self::PROMINENT_WORDS_TABLE)
            ->where(
                $queryBuilder->expr()->in(
                    'stem',
                    $queryBuilder->createNamedParameter($stems, Connection::PARAM_STR_ARRAY)
                ),
                $queryBuilder->expr()->eq('sys_language_uid', $this->languageId),
                $queryBuilder->expr()->eq('site', $this->site)
            )
            ->setMaxResults($batchSize)
            ->setFirstResult(($page - 1) * $batchSize)
            ->execute()
            ->fetchAllAssociative();
    }

    protected function getProminentWords(array $records): array
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable(self::PROMINENT_WORDS_TABLE);
        $orStatements = [];
        foreach ($records as $record) {
            $orStatements[] = $queryBuilder->expr()->andX(
                $queryBuilder->expr()->eq('pid', $record['pid']),
                $queryBuilder->expr()->eq('tablenames', $queryBuilder->createNamedParameter($record['tablenames'])),
                $queryBuilder->expr()->eq(
                    'sys_language_uid',
                    $queryBuilder->createNamedParameter($this->languageId, Connection::PARAM_INT)
                )
            );
        }
        return $queryBuilder->select('stem', 'weight', 'pid', 'tablenames', 'uid_foreign')
            ->from(self::PROMINENT_WORDS_TABLE)
            ->where(
                $queryBuilder->expr()->orX(...$orStatements)
            )
            ->execute()
            ->fetchAllAssociative();
    }

    protected function groupWordsByRecord(array $candidateWords): array
    {
        $candidateWordsByRecords = [];
        foreach ($candidateWords as $candidateWord) {
            $recordKey = $candidateWord['uid_foreign'] . '-' . $candidateWord['tablenames'];
            $candidateWordsByRecords[$recordKey][$candidateWord['stem']] = [
                'weight' => (int)$candidateWord['weight'],
                'df' => (int)$candidateWord['df']
            ];
        }
        return $candidateWordsByRecords;
    }

    protected function calculateScoreForIndexable(
        array $requestData,
        float $requestVectorLength,
        array $candidateData
    ): float {
        $rawScore = $this->computeRawScore($requestData, $candidateData);
        $candidateVectorLength = $this->computeVectorLength($candidateData);
        return $this->normalizeScore($rawScore, $candidateVectorLength, $requestVectorLength);
    }

    protected function computeRawScore(array $requestData, array $candidateData): float
    {
        $rawScore = 0;
        foreach ($candidateData as $stem => $candidateWordData) {
            if (!array_key_exists($stem, $requestData)) {
                continue;
            }

            $wordFromRequestWeight = $requestData[$stem]['weight'];
            $wordFromRequestDf = $requestData[$stem]['df'];
            $candidateWeight = $candidateWordData['weight'];
            $canidateDf = $candidateWordData['df'];

            $tfIdfFromRequest = $this->computeTfIdfScore($wordFromRequestWeight, $wordFromRequestDf);
            $tfIdfFromDatabase = $this->computeTfIdfScore($candidateWeight, $canidateDf);

            $rawScore += ($tfIdfFromRequest * $tfIdfFromDatabase);
        }
        return (float)$rawScore;
    }

    protected function normalizeScore(float $rawScore, float $vectorLengthCandidate, float $vectorLengthRequest): float
    {
        $normalizingFactor = $vectorLengthRequest * $vectorLengthCandidate;
        if ($normalizingFactor === 0.0) {
            // We can't divide by 0, so set the score to 0 instead.
            return 0;
        }
        return (float)($rawScore / $normalizingFactor);
    }

    protected function getTopSuggestions(array $scores): array
    {
        // Sort the indexables by descending score.
        uasort(
            $scores,
            static function ($score1, $score2) {
                if ($score1 === $score2) {
                    return 0;
                }
                return ($score1 < $score2) ? 1 : -1;
            }
        );

        // Take the top $limit suggestions, while preserving their ids specified in the keys of the array elements.
        return \array_slice($scores, 0, 20, true);
    }

    protected function linkRecords(array $scores, array $currentLinks): array
    {
        $links = [];
        foreach ($scores as $record => $score) {
            [$uid, $table] = explode('-', $record);
            if ($table === 'pages' && (int)$uid === $this->excludePageId) {
                continue;
            }

            $data = BackendUtility::getRecord($table, $uid);
            if ($data === null) {
                continue;
            }
            if ($this->languageId > 0
                && ($overlay = $this->pageRepository->getRecordOverlay($table, $data, $this->languageId, 'mixed'))) {
                $data = $overlay;
            }

            $labelField = $GLOBALS['TCA'][$table]['ctrl']['label'];

            $links[$record] = [
                'label' => $data[$labelField],
                'recordType' => $this->getRecordType($table),
                'id' => $uid,
                'table' => $table,
                'cornerstone' => (int)$data['tx_yoastseo_cornerstone'],
                'score' => $score,
                'active' => isset($currentLinks[$record])
            ];
        }
        $this->sortSuggestions($links);

        $cornerStoneSuggestions = $this->filterSuggestions($links, true);
        $nonCornerStoneSuggestions = $this->filterSuggestions($links, false);

        return array_merge_recursive([], $cornerStoneSuggestions, $nonCornerStoneSuggestions);
    }

    protected function sortSuggestions(array &$links): void
    {
        usort(
            $links,
            static function ($suggestion1, $suggestion2) {
                if ($suggestion1['score'] === $suggestion2['score']) {
                    return 0;
                }

                return ($suggestion1['score'] < $suggestion2['score']) ? 1 : -1;
            }
        );
    }

    protected function filterSuggestions(array $links, bool $cornerstone): array
    {
        return \array_filter(
            $links,
            static function ($suggestion) use ($cornerstone) {
                return (bool)$suggestion['cornerstone'] === $cornerstone;
            }
        );
    }

    protected function getCurrentContentLinks(string $content): array
    {
        $currentLinks = [];
        preg_match_all('/<a href="t3:\/\/(.*)\?uid=([\d]+)/', $content, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $key = (int)$match[2] . '-' . str_replace('page', 'pages', $match[1]);
            $currentLinks[$key] = true;
        }
        return $currentLinks;
    }

    protected function getSiteRootPageId(int $pageUid): int
    {
        try {
            $site = GeneralUtility::makeInstance(SiteFinder::class)->getSiteByPageId($pageUid);
            return $site->getRootPageId();
        } catch (SiteNotFoundException $e) {
            return 0;
        }
    }

    protected function getRecordType(string $table): string
    {
        return $this->getLanguageService()->sL(
            $GLOBALS['TCA'][$table]['ctrl']['title']
        );
    }

    protected function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }
}
