<?php
declare(strict_types=1);
namespace YoastSeoForTypo3\YoastSeo\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Exception\SiteNotFoundException;
use TYPO3\CMS\Core\Http\HtmlResponse;
use TYPO3\CMS\Core\Http\JsonResponse;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Page\PageRepository;
use YoastSeoForTypo3\YoastSeo\Service\PreviewService;
use YoastSeoForTypo3\YoastSeo\Utility\YoastUtility;

/**
 * Class AjaxController
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

        if (!isset($queryParams['pageId'], $queryParams['languageId'], $queryParams['additionalGetVars'])) {
            return new JsonResponse();
        }

        $previewService = GeneralUtility::makeInstance(PreviewService::class);
        $content = $previewService->getPreviewData(
            $this->getUriToCheck(
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
     * @param int    $pageId
     * @param int    $languageId
     * @param string $additionalGetVars
     * @return string
     */
    protected function getUriToCheck(int $pageId, int $languageId, string $additionalGetVars): string
    {
        if (!empty($additionalGetVars)) {
            $additionalGetVars = urldecode($additionalGetVars);
        }

        $rootLine = BackendUtility::BEgetRootLine($pageId);
        // Mount point overlay: Set new target page id and mp parameter
        $pageRepository = GeneralUtility::makeInstance(PageRepository::class);
        $finalPageIdToShow = $pageId;
        $mountPointInformation = $pageRepository->getMountPointInfo($pageId);
        if ($mountPointInformation && $mountPointInformation['overlay']) {
            // New page id
            $finalPageIdToShow = $mountPointInformation['mount_pid'];
            $additionalGetVars .= '&MP=' . $mountPointInformation['MPvar'];
        }

        $siteFinder = GeneralUtility::makeInstance(SiteFinder::class);
        try {
            $site = $siteFinder->getSiteByPageId($finalPageIdToShow, $rootLine);
        } catch (SiteNotFoundException $e) {
            return '';
        }

        $additionalQueryParams = [];
        parse_str($additionalGetVars, $additionalQueryParams);
        $additionalQueryParams['_language'] = $site->getLanguageById($languageId);
        $uriToCheck = YoastUtility::fixAbsoluteUrl(
            (string)$site->getRouter()->generateUri($finalPageIdToShow, $additionalQueryParams)
        );

        if (is_array($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS'][self::class]['urlToCheck'])) {
            foreach ($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS'][self::class]['urlToCheck'] as $_funcRef) {
                $_params = [
                    'urlToCheck' => $uriToCheck,
                    'site' => $site,
                    'finalPageIdToShow' => $finalPageIdToShow,
                    'languageId' => $languageId
                ];

                $uriToCheck = GeneralUtility::callUserFunction($_funcRef, $_params, $this);
            }
        }
        return (string)$uriToCheck;
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
