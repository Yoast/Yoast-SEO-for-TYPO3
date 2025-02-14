<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Service\Ajax;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Error\Http\BadRequestException;
use TYPO3\CMS\Core\Http\JsonResponse;
use YoastSeoForTypo3\YoastSeo\Service\Crawler\CrawlerService;

class CrawlerHandler extends AbstractAjaxHandler
{
    public function __construct(
        protected CrawlerService $crawlerService
    ) {}

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if ($request->getAttribute('handlerRequest') === 'determine') {
            return $this->determinePages($request);
        }
        if ($request->getAttribute('handlerRequest') === 'index') {
            return $this->indexPages($request);
        }
        throw new BadRequestException('Invalid request');
    }

    protected function determinePages(ServerRequestInterface $request): ResponseInterface
    {
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

    protected function indexPages(ServerRequestInterface $request): ResponseInterface
    {
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
}
