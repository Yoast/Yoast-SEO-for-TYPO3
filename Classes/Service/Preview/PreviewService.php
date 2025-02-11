<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Service\Preview;

class PreviewService
{
    public function __construct(
        protected UrlContentFetcher $urlContentFetcher,
        protected HttpOptionSetter $httpOptionSetter,
        protected ContentParser $contentParser
    ) {}

    public function getPreviewData(string $uriToCheck, int $pageId): string
    {
        $this->httpOptionSetter->setHttpOptions();

        try {
            $content = $this->urlContentFetcher->fetch($uriToCheck);
            $data = $this->contentParser->parse($content, $uriToCheck, $pageId);
        } catch (\Exception $e) {
            $data = [
                'error' => [
                    'uriToCheck' => $uriToCheck,
                    'statusCode' => $e->getMessage(),
                ],
            ];
        }

        try {
            return (string)json_encode($data, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return '';
        }
    }
}
