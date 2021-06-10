<?php
declare(strict_types=1);
namespace YoastSeoForTypo3\YoastSeo\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Http\HtmlResponse;
use TYPO3\CMS\Core\Http\JsonResponse;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use YoastSeoForTypo3\YoastSeo\Service\PreviewService;
use YoastSeoForTypo3\YoastSeo\Service\UrlService;

/**
 * Class AjaxController
 */
class AjaxController
{
    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Exception
     */
    public function previewAction(
        ServerRequestInterface $request
    ): ResponseInterface {
        $queryParams = $request->getQueryParams();

        if (!isset($queryParams['pageId'], $queryParams['languageId'], $queryParams['additionalGetVars'])) {
            return new JsonResponse();
        }

        $previewService = GeneralUtility::makeInstance(PreviewService::class);
        $urlService = GeneralUtility::makeInstance(UrlService::class);
        $content = $previewService->getPreviewData(
            $urlService->getUriToCheck(
                (int)$queryParams['pageId'],
                (int)$queryParams['languageId'],
                (string)$queryParams['additionalGetVars']
            ),
            (int)$queryParams['pageId']
        );

        return new HtmlResponse($content);
    }

    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface|null $response
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function saveScoresAction(
        ServerRequestInterface $request,
        ResponseInterface $response = null
    ): ResponseInterface {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        if (!empty($data['table']) && !empty($data['uid'])) {
            $this->saveScores($data);
        }
        if ($response === null) {
            return new JsonResponse(['OK']);
        }

        $response->getBody()->write(json_encode(['OK']));
        return $response;
    }

    /**
     * Save scores
     *
     * @param array $data
     */
    protected function saveScores(array $data): void
    {
        $connection = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable($data['table']);
        $row = $connection->select(['*'], $data['table'], ['uid' => (int)$data['uid']], [], [], 1)->fetch();

        if ($row !== false && isset($row['tx_yoastseo_score_readability'], $row['tx_yoastseo_score_seo'])) {
            $connection->update($data['table'], [
                'tx_yoastseo_score_readability' => (string)$data['readabilityScore'],
                'tx_yoastseo_score_seo' => (string)$data['seoScore']
            ], ['uid' => (int)$data['uid']]);
        }
    }
}
