<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Service\Preview;

use YoastSeoForTypo3\YoastSeo\Service\Preview\ContentExtractors\BaseUrlParserInterface;
use YoastSeoForTypo3\YoastSeo\Service\Preview\ContentExtractors\BodyProcessorInterface;
use YoastSeoForTypo3\YoastSeo\Service\Preview\ContentExtractors\ContentMetadataExtractorInterface;
use YoastSeoForTypo3\YoastSeo\Service\Preview\ContentExtractors\FaviconExtractorInterface;
use YoastSeoForTypo3\YoastSeo\Service\Preview\ContentExtractors\TitleConfigurationExtractorInterface;

class ContentParser
{
    public function __construct(
        protected BaseUrlParserInterface $baseUrlParser,
        protected BodyProcessorInterface $bodyProcessor,
        protected ContentMetadataExtractorInterface $contentMetadataExtractor,
        protected FaviconExtractorInterface $faviconExtractor,
        protected TitleConfigurationExtractorInterface $titleConfigurationExtractor,
    ) {}

    /**
     * @return array<string, int|string>
     */
    public function parse(string $content, string $uriToCheck, int $pageId): array
    {
        $urlParts = parse_url((string)preg_replace('/\/$/', '', $uriToCheck));
        $baseUrl = $this->baseUrlParser->getBaseUrl($urlParts);
        $url = $baseUrl . ($urlParts['path'] ?? '');

        $titleConfiguration = $this->titleConfigurationExtractor->getTitleConfiguration($content);

        return [
            'id' => $pageId,
            'url' => $url,
            'baseUrl' => $baseUrl,
            'slug' => '/',
            'title' => $this->contentMetadataExtractor->getTitle($content),
            'description' => $this->contentMetadataExtractor->getDescription($content),
            'locale' => $this->contentMetadataExtractor->getLocale($content),
            'body' => $this->bodyProcessor->getBody($content),
            'faviconSrc' => $this->faviconExtractor->getFaviconSrc($baseUrl, $content),
            'pageTitlePrepend' => $titleConfiguration['titlePrepend'],
            'pageTitleAppend' => $titleConfiguration['titleAppend'],
        ];
    }
}
