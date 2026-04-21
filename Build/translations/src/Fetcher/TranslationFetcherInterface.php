<?php

/**
 * This file is part of the "yoast_seo" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace YoastSeoForTypo3\Translations\Fetcher;

use Psr\Http\Message\ResponseInterface;

interface TranslationFetcherInterface
{
    public function fetchTranslations(): array;
    public function fetchTranslationPo(string $locale, string $slug): ResponseInterface;
}
