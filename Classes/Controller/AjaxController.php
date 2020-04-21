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

/**
 * Class AjaxController
 * @package YoastSeoForTypo3\YoastSeo\Controller
 */
class AjaxController
{
    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Exception
     */
    public function previewAction(
        ServerRequestInterface $request
    ): ResponseInterface {
        $queryParams = $request->getQueryParams();

        $previewService = GeneralUtility::makeInstance(PreviewService::class);
        $content = $previewService->getPreviewData(
            $queryParams['uriToCheck'],
            (int)$queryParams['pageId']
        );

        return new HtmlResponse($content);
    }

    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function saveScoresAction(
        ServerRequestInterface $request,
        ResponseInterface $response = null
    ): ResponseInterface {
        $json = file_get_contents('php://input');
        $data = json_decode($json);

        if (!empty($data->table) && !empty($data->uid)) {
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
     * @param $data
     */
    protected function saveScores($data)
    {
        $connection = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable($data->table);
        $row = $connection->select(['*'], $data->table, ['uid' => (int)$data->uid], [], [], 1)->fetch();

        if ($row !== false && isset($row['tx_yoastseo_score_readability'], $row['tx_yoastseo_score_seo'])) {
            $connection->update($data->table, [
                'tx_yoastseo_score_readability' => (string)$data->readabilityScore,
                'tx_yoastseo_score_seo' => (string)$data->seoScore
            ], ['uid' => (int)$data->uid]);
        }
    }
}
