<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Service\Ajax;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Http\JsonResponse;
use YoastSeoForTypo3\YoastSeo\Service\LinkingSuggestionsService;

class InternalLinkingSuggestionsHandler extends AbstractAjaxHandler
{
    public function __construct(
        protected LinkingSuggestionsService $linkingSuggestionsService
    ) {}

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
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
}
