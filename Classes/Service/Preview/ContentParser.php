<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Service\Preview;

class ContentParser
{
    /**
     * @return array<string, int|string>
     */
    public function parse(string $content, string $uriToCheck, int $pageId): array
    {
        $urlParts = parse_url((string)preg_replace('/\/$/', '', $uriToCheck));
        $baseUrl = $this->getBaseUrl($urlParts);
        $url = $baseUrl . ($urlParts['path'] ?? '');

        $titleConfiguration = $this->getTitleConfiguration($content);

        return [
            'id' => $pageId,
            'url' => $url,
            'baseUrl' => $baseUrl,
            'slug' => '/',
            'title' => $this->getTitle($content),
            'description' => $this->getDescription($content),
            'locale' => $this->getLocale($content),
            'body' => $this->getBody($content),
            'faviconSrc' => $this->getFaviconSrc($baseUrl, $content),
            'pageTitlePrepend' => $titleConfiguration['titlePrepend'],
            'pageTitleAppend' => $titleConfiguration['titleAppend'],
        ];
    }

    protected function getBaseUrl(mixed $urlParts): string
    {
        if (!is_array($urlParts)) {
            return '://';
        }
        if ($urlParts['port'] ?? false) {
            return (isset($urlParts['scheme']) ? $urlParts['scheme'] . ':' : '') . '//' . ($urlParts['host'] ?? '') . ':' . $urlParts['port'];
        }
        return (isset($urlParts['scheme']) ? $urlParts['scheme'] . ':' : '') . '//' . ($urlParts['host'] ?? '');
    }

    protected function getTitle(string $content): string
    {
        $title = '';
        $titleFound = preg_match("/<title[^>]*>(.*?)<\/title>/is", $content, $matchesTitle);

        if ($titleFound) {
            $title = $matchesTitle[1];
        }

        return strip_tags(html_entity_decode($title));
    }

    protected function getDescription(string $content): string
    {
        $metaDescription = '';
        $descriptionFound = preg_match(
            "/<meta[^>]*name=[\" | \']description[\"|\'][^>]*content=[\"]([^\"]*)[\"][^>]*>/i",
            $content,
            $matchesDescription
        );

        if ($descriptionFound) {
            $metaDescription = $matchesDescription[1];
        }

        return strip_tags(html_entity_decode($metaDescription));
    }

    protected function getLocale(string $content): string
    {
        $locale = 'en';
        $localeFound = preg_match('/<html[^>]*lang="([a-z\-A-Z]*)"/is', $content, $matchesLocale);

        if ($localeFound) {
            [$locale] = explode('-', trim($matchesLocale[1]));
        }

        return $locale;
    }

    protected function getBody(string $content): string
    {
        $body = '';

        $bodyFound = preg_match("/<body[^>]*>(.*)<\/body>/is", $content, $matchesBody);

        if ($bodyFound) {
            $body = $matchesBody[1];

            preg_match_all(
                '/<!--\s*?TYPO3SEARCH_begin\s*?-->.*?<!--\s*?TYPO3SEARCH_end\s*?-->/mis',
                $body,
                $indexableContents
            );

            if (is_array($indexableContents[0]) && !empty($indexableContents[0])) {
                $body = implode('', $indexableContents[0]);
            }
        }

        return $this->prepareBody($body);
    }

    protected function prepareBody(string $body): string
    {
        $body = $this->stripTagsContent($body, '<script><noscript>');
        $body = preg_replace(['/\s?\n\s?/', '/\s{2,}/'], [' ', ' '], $body);
        $body = strip_tags((string)$body, '<h1><h2><h3><h4><h5><p><a><img>');

        return trim($body);
    }

    protected function stripTagsContent(string $text, string $tags = ''): string
    {
        preg_match_all('/<(.+?)[\s]*\/?[\s]*>/si', trim($tags), $foundTags);
        $tagsArray = array_unique($foundTags[1]);

        if (is_array($tagsArray) && count($tagsArray) > 0) {
            return (string)preg_replace('@<(' . implode('|', $tagsArray) . ')\b.*?>.*?</\1>@si', '', $text);
        }

        return $text;
    }

    protected function getFaviconSrc(string $baseUrl, string $content): string
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

    /**
     * @return array{titlePrepend: string, titleAppend: string}
     */
    protected function getTitleConfiguration(string $content): array
    {
        $prepend = $append = '';
        preg_match('/<meta name=\"x-yoast-title-config\" value=\"([^"]*)\"/i', $content, $matchesTitleConfig);
        if (count($matchesTitleConfig) > 1) {
            [$prepend, $append] = explode('|||', (string)$matchesTitleConfig[1]);
        }
        return [
            'titlePrepend' => $prepend,
            'titleAppend' => $append,
        ];
    }
}
