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
use MaxServ\FrontendRequest\PageParser\ParserInterface;

readonly class TitleConfigurationParser implements ParserInterface
{
    public function getIdentifier(): string
    {
        return 'titleConfiguration';
    }

    /**
     * @return array<string, string>
     */
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
