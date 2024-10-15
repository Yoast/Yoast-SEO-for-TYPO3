<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Controller;

use Throwable;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Http\HtmlResponse;
use TYPO3\CMS\Core\Http\JsonResponse;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use YoastSeoForTypo3\YoastSeo\Service\CrawlerService;
use YoastSeoForTypo3\YoastSeo\Service\LinkingSuggestionsService;
use YoastSeoForTypo3\YoastSeo\Service\PreviewService;
use YoastSeoForTypo3\YoastSeo\Service\ProminentWordsService;
use YoastSeoForTypo3\YoastSeo\Service\UrlService;

class AjaxController
{
    public function __construct(
        protected PreviewService $previewService,
        protected UrlService $urlService,
        protected ProminentWordsService $prominentWordsService,
        protected LinkingSuggestionsService $linkingSuggestionsService,
        protected CrawlerService $crawlerService
    ) {
    }

    public function previewAction(
        ServerRequestInterface $request
    ): ResponseInterface {
        $queryParams = $request->getQueryParams();

        if (!isset($queryParams['pageId'], $queryParams['languageId'], $queryParams['additionalGetVars'])) {
            $json = $this->getJsonData($request);
            if (isset($json['pageId'], $json['languageId'], $json['additionalGetVars'])) {
                $queryParams = $json;
            } else {
                return new JsonResponse([]);
            }
        }

        $content = $this->previewService->getPreviewData(
            $this->urlService->getUriToCheck(
                (int)$queryParams['pageId'],
                (int)$queryParams['languageId'],
                (string)$queryParams['additionalGetVars']
            ),
            (int)$queryParams['pageId']
        );

        return new HtmlResponse($content);
    }

    public function saveScoresAction(
        ServerRequestInterface $request
    ): ResponseInterface {
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
        $connection = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable($data['table']);
        try {
            $row = $connection->select(['*'], $data['table'], ['uid' => (int)$data['uid']], [],
                [],
                1)->fetchAssociative();
        } catch (Throwable) {
            return;
        }

        if ($row !== false && isset($row['tx_yoastseo_score_readability'], $row['tx_yoastseo_score_seo'])) {
            $connection->update($data['table'], [
                'tx_yoastseo_score_readability' => (string)$data['readabilityScore'],
                'tx_yoastseo_score_seo' => (string)$data['seoScore'],
            ], ['uid' => (int)$data['uid']]);
        }
    }

    public function promimentWordsAction(
        ServerRequestInterface $request
    ): ResponseInterface {
        $data = $this->getJsonData($request);

        if (isset($data['words'], $data['uid'])) {
            $this->prominentWordsService->saveProminentWords(
                (int)$data['uid'],
                isset($data['pid']) ? (int)$data['pid'] : null,
                $data['table'] ?? 'pages',
                (int)($data['languageId'] ?? 0),
                (array)$data['words']
            );
        }

        return new JsonResponse(['OK']);
    }

    public function internalLinkingSuggestionsAction(
        ServerRequestInterface $request
    ): ResponseInterface {
        $data = $this->getJsonData($request);

        $words = $data['words'] ?? [];
        $excludedPageId = (int)($data['excludedPage'] ?? 0);
        $languageId = (int)($data['languageId'] ?? 0);
        $content = (string)($data['content'] ?? '');

        $links = $this->linkingSuggestionsService->getLinkingSuggestions(
            $words,
            $excludedPageId,
            $languageId,
            $content
        );

        return new JsonResponse([
            'OK',
            'links' => $links,
            'excludedPage' => $excludedPageId,
            'languageId' => $languageId,
        ]);
    }

    public function crawlerDeterminePages(
        ServerRequestInterface $request
    ): ResponseInterface {
        $crawlerData = $this->getCrawlerRequestData($request);
        $amount = $this->crawlerService->getAmountOfPages($crawlerData['site'], $crawlerData['language']);
        if ($amount > 0) {
            return new JsonResponse([
                'amount' => $amount,
            ]);
        }
        return new JsonResponse([
            'error' => 'No pages found to analyse',
        ]);
    }

    public function crawlerIndexPages(
        ServerRequestInterface $request
    ): ResponseInterface {
        $crawlerData = $this->getCrawlerRequestData($request);
        $indexInformation = $this->crawlerService->getIndexInformation(
            $crawlerData['site'],
            $crawlerData['language'],
            $crawlerData['offset']
        );
        if (count($indexInformation['pages']) === 0) {
            return new JsonResponse(['status' => 'finished', 'total' => $indexInformation['total']]);
        }
        return new JsonResponse($indexInformation);
    }

    /**
     * @return array<string, int>
     */
    protected function getCrawlerRequestData(ServerRequestInterface $request): array
    {
        $crawlerData = $this->getJsonData($request);
        if (!isset($crawlerData['site'], $crawlerData['language'])) {
            die(json_encode(['error' => 'No site and language provided by request']));
        }
        return [
            'site' => (int)$crawlerData['site'],
            'language' => (int)$crawlerData['language'],
            'offset' => (int)($crawlerData['offset'] ?? 0),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function getJsonData(ServerRequestInterface $request): array
    {
        $body = $request->getBody()->getContents();
        return json_decode($body, true);
    }
}
