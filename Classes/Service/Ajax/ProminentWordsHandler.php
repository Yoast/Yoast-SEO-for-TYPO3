<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Service\Ajax;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Http\JsonResponse;
use YoastSeoForTypo3\YoastSeo\Service\ProminentWordsService;

class ProminentWordsHandler extends AbstractAjaxHandler
{
    public function __construct(
        protected ProminentWordsService $prominentWordsService
    ) {}

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
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
}
