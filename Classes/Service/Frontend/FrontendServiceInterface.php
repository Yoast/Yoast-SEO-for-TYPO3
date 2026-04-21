<?php

/**
 * This file is part of the "yoast_seo" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Service\Frontend;

interface FrontendServiceInterface
{
    public const CACHE_IDENTIFIER = 'tx_yoast_seo_cache_identifier';
    public function isFrontendRequest(): bool;
    /**
     * @return array<string, mixed>
     */
    public function getTyposcriptConfiguration(): array;
    public function getPageUid(): int;
    /**
     * @return array<int, array<string, mixed>>
     */
    public function getRootLine(): array;
    public function isSiteRoot(): bool;
    public function getCacheIdentifier(string $suffix): string;
    public function getCacheTimeout(): int;
}
