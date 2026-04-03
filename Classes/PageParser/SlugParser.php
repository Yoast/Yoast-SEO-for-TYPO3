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

readonly class SlugParser implements ParserInterface
{
    public function getIdentifier(): string
    {
        return 'slug';
    }

    public function parse(string $html, RequestContext $context): string
    {
        $urlParts = $this->getUrlParts($context);
        return (string)($urlParts['path'] ?? '');
    }

    /**
     * @return array<string, int|string>|null
     */
    protected function getUrlParts(RequestContext $context): ?array
    {
        $urlParts = parse_url((string)preg_replace('/\/$/', '', $context->getUrl()));
        return is_array($urlParts) ? $urlParts : null;
    }
}
