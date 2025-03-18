<?php

/**
 * This file is part of the "yoast_seo" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace YoastSeoForTypo3\Translations\Fetcher;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

class TranslationFetcher implements TranslationFetcherInterface
{
    public function __construct(
        private Client $client
    ) {}

    public function fetchTranslations(): array
    {
        $response = $this->client->request('GET');
        return json_decode($response->getBody()->getContents(), true)['translation_sets'] ?? [];
    }

    public function fetchTranslationPo(string $locale, string $slug): ResponseInterface
    {
        return $this->client->request('GET', "$locale/$slug/export-translations?format=po");
    }
}
