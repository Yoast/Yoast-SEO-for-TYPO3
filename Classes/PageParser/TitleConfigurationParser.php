<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\PageParser;

use MaxServ\FrontendRequest\Dto\RequestContext;
use MaxServ\FrontendRequest\PageParser\ParserInterface;

class TitleConfigurationParser implements ParserInterface
{
    public function getIdentifier(): string
    {
        return 'titleConfiguration';
    }

    public function parse(string $html, RequestContext $context): array
    {
        $prepend = $append = '';
        preg_match('/<meta name=\"x-yoast-title-config\" value=\"([^"]*)\"/i', $html, $matchesTitleConfig);
        if (count($matchesTitleConfig) > 1) {
            [$prepend, $append] = explode('|||', (string)$matchesTitleConfig[1]);
        }
        return [
            'prepend' => $prepend,
            'append' => $append,
        ];
    }
}
