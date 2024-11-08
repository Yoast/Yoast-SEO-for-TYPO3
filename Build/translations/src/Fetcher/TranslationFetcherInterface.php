<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\Translations\Fetcher;

use Psr\Http\Message\ResponseInterface;

interface TranslationFetcherInterface
{
    public function fetchTranslations(): array;
    public function fetchTranslationPo(string $locale, string $slug): ResponseInterface;
}
