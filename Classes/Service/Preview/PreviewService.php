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

    public function getPreviewData(string $uriToCheck, int $pageId): array
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

        return $data;
    }
}
