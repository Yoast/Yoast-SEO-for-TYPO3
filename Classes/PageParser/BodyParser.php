<?php

/**
 * This file is part of the "yoast_seo" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\PageParser;

use MaxServ\FrontendRequest\Dto\RequestContext;

class BodyParser extends \MaxServ\FrontendRequest\PageParser\Parser\BodyParser
{
    protected const INDEXABLE_PATTERN = '/<!--\s*TYPO3SEARCH_(begin|end)\s*-->/i';
    protected const ALLOWED_TAGS = '<h1><h2><h3><h4><h5><h6><p><a><img>';
    protected const STRIP_WITH_CONTENT_TAGS = '<script><noscript>';

    public function parse(string $html, RequestContext $context): string
    {
        $body = parent::parse($html, $context);

        $sections = $this->extractIndexableSections($body);
        if ($sections !== []) {
            $body = implode(' ', array_map(trim(...), $sections));
        }

        return $this->prepareBody($body);
    }

    protected function prepareBody(string $body): string
    {
        $body = $this->stripTagsContent($body, self::STRIP_WITH_CONTENT_TAGS);
        $body = (string)preg_replace('/\s+/', ' ', $body);
        $body = strip_tags($body, self::ALLOWED_TAGS);

        return trim($body);
    }

    protected function stripTagsContent(string $text, string $tags = ''): string
    {
        if ($tags === '') {
            return $text;
        }

        preg_match_all('/<([a-z][a-z0-9]*)\b/i', $tags, $found);
        $names = array_unique($found[1]);
        if ($names === []) {
            return $text;
        }

        $pattern = '@<(' . implode('|', array_map('preg_quote', $names)) . ')\b[^>]*>.*?</\1\s*>@is';
        return (string)preg_replace($pattern, '', $text);
    }

    /**
     * @return list<string>
     */
    protected function extractIndexableSections(string $body): array
    {
        if (!preg_match_all(self::INDEXABLE_PATTERN, $body, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE)) {
            return [];
        }

        $sections = [];
        $depth = 0;
        $sectionStart = null;

        foreach ($matches as $match) {
            $markerText = $match[0][0];
            $markerOffset = (int)$match[0][1];
            $markerType = strtolower($match[1][0]);

            if ($markerType === 'begin') {
                if ($depth === 0) {
                    $sectionStart = $markerOffset + strlen($markerText);
                }
                $depth++;
                continue;
            }

            if ($depth === 0) {
                continue;
            }
            $depth--;

            if ($depth === 0 && $sectionStart !== null) {
                $sections[] = substr($body, $sectionStart, $markerOffset - $sectionStart);
                $sectionStart = null;
            }
        }

        return $sections;
    }
}
