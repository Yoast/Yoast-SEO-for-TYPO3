<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Service\Preview\ContentExtractors;

class ContentMetadataExtractor implements ContentMetadataExtractorInterface
{
    public function getTitle(string $content): string
    {
        $title = '';
        $titleFound = preg_match("/<title[^>]*>(.*?)<\/title>/is", $content, $matchesTitle);

        if ($titleFound) {
            $title = $matchesTitle[1];
        }

        return strip_tags(html_entity_decode($title));
    }

    public function getDescription(string $content): string
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

    public function getLocale(string $content): string
    {
        $locale = 'en';
        $localeFound = preg_match('/<html[^>]*lang="([a-z\-A-Z]*)"/is', $content, $matchesLocale);

        if ($localeFound) {
            [$locale] = explode('-', trim($matchesLocale[1]));
        }

        return $locale;
    }
}
