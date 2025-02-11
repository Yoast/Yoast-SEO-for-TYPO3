<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Service\Preview\ContentExtractors;

class FaviconExtractor implements FaviconExtractorInterface
{
    public function getFaviconSrc(string $baseUrl, string $content): string
    {
        $faviconSrc = $baseUrl . '/favicon.ico';
        $favIconFound = preg_match('/<link rel=\"shortcut icon\" href=\"([^"]*)\"/i', $content, $matchesFavIcon);
        if ($favIconFound) {
            $faviconSrc = str_contains($matchesFavIcon[1], '://') ? $matchesFavIcon[1] : $baseUrl . $matchesFavIcon[1];
        }
        $favIconHeader = @get_headers($faviconSrc);
        if (($favIconHeader[0] ?? '') === 'HTTP/1.1 404 Not Found') {
            $faviconSrc = '';
        }
        return $faviconSrc;
    }
}
