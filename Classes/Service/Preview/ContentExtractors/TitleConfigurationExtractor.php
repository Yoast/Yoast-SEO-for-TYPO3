<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Service\Preview\ContentExtractors;

class TitleConfigurationExtractor implements TitleConfigurationExtractorInterface
{
    /**
     * @return array{titlePrepend: string, titleAppend: string}
     */
    public function getTitleConfiguration(string $content): array
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
