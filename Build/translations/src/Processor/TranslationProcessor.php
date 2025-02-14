<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\Translations\Processor;

use Sepia\PoParser\Parser;
use Sepia\PoParser\SourceHandler\StringSource;
use YoastSeoForTypo3\Translations\Fetcher\TranslationFetcherInterface;
use YoastSeoForTypo3\Translations\Utility\FileSaverUtility;

class TranslationProcessor
{
    public function __construct(
        private TranslationFetcherInterface $fetcher,
        private string $domain
    ) {
    }

    public function processTranslations(): void
    {
        foreach ($this->fetcher->fetchTranslations() as $translation) {
            if (!$this->isTranslationValid($translation)) {
                continue;
            }

            $poResponse = $this->fetcher->fetchTranslationPo($translation['locale'], $translation['slug']);
            $poContent = $poResponse->getBody()->getContents();
            $this->parseAndSaveTranslation($poContent, $translation);
        }
    }

    private function isTranslationValid(array $translation): bool
    {
        return isset($translation['slug'], $translation['name'], $translation['locale'], $translation['wp_locale']) &&
            ((int) ($translation['current_count'] ?? 0) > 0) &&
            ((int) ($translation['percent_translated'] ?? 0) >= 50) &&
            !str_contains($translation['wp_locale'], 'formal');
    }

    private function parseAndSaveTranslation(string $poContent, array $translation): void
    {
        $stringSource = new StringSource($poContent);
        $poParser = new Parser($stringSource);
        $catalog = $poParser->parse();

        $messages = [
            '' => [
                'domain' => $this->domain,
                'lang' => $translation['locale'],
                'plural_forms' => $catalog->getHeaders()['Plural-Forms'] ?? 'nplurals=2; plural=(n != 1);',
            ]
        ];

        foreach ($catalog->getEntries() as $entry) {
            if (empty($entry->getMsgStr())) {
                continue;
            }
            $messages[$entry->getMsgId()] = [$entry->getMsgStr()];
        }

        $result = [
            'domain' => $this->domain,
            'locale_data' => [$this->domain => $messages]
        ];

        $filePath = __DIR__ . '/../../../../Resources/Private/Language/wordpress-seo-' . $translation['wp_locale'] . '.json';
        FileSaverUtility::saveToFile($filePath, json_encode($result));
    }
}
