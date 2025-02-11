<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Service\Ajax;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Http\HtmlResponse;
use TYPO3\CMS\Core\Http\JsonResponse;
use YoastSeoForTypo3\YoastSeo\Service\Preview\PreviewService;
use YoastSeoForTypo3\YoastSeo\Service\UrlService;

class PreviewHandler extends AbstractAjaxHandler
{
    public function __construct(
        protected PreviewService $previewService,
        protected UrlService $urlService,
    ) {}

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
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
}
