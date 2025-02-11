<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Service\Preview\ContentExtractors;

class BodyProcessor implements BodyProcessorInterface
{
    public function getBody(string $content): string
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

        if (count($tagsArray) > 0) {
            return (string)preg_replace('@<(' . implode('|', $tagsArray) . ')\b.*?>.*?</\1>@si', '', $text);
        }

        return $text;
    }
}
