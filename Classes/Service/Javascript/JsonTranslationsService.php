<?php

/**
 * This file is part of the "yoast_seo" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Service\Javascript;

use TYPO3\CMS\Core\SingletonInterface;
use YoastSeoForTypo3\YoastSeo\Service\LocaleService;

class JsonTranslationsService implements SingletonInterface
{
    /** @var array<string, mixed> */
    protected array $translations = [];

    public function __construct(
        protected readonly LocaleService $localeService
    ) {}

    public function addTranslations(): void
    {
        $this->translations = $this->localeService->getTranslations();
    }

    public function render(): string
    {
        if (!empty($this->translations)) {
            return 'window.YoastTranslations = ' . json_encode([$this->translations]) . ';';
        }
        return '';
    }
}
