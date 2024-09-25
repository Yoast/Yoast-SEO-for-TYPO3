<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\ViewHelpers;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use YoastSeoForTypo3\YoastSeo\Service\CrawlerService;

class CrawlerProgressViewHelper extends AbstractViewHelper
{
    public function __construct(
        protected CrawlerService $crawlerService
    ) {}

    public function initializeArguments(): void
    {
        $this->registerArgument('site', 'integer', 'Site root pageid', true);
        $this->registerArgument('language', 'integer', 'Language id', true);
    }

    /**
     * @return array{percentage: int|float, offset: int, total: int}|null
     */
    public function render(): ?array
    {
        $progressInformation = $this->crawlerService->getProgressInformation(
            $this->arguments['site'],
            $this->arguments['language']
        );

        if (!isset($progressInformation['offset'], $progressInformation['total'])) {
            return null;
        }

        if ($progressInformation['offset'] > $progressInformation['total']) {
            return [
                'percentage' => 100,
                'offset' => $progressInformation['total'],
                'total' => $progressInformation['total'],
            ];
        }
        return [
            'percentage' => round(($progressInformation['offset'] / $progressInformation['total']) * 100),
            'offset' => $progressInformation['offset'],
            'total' => $progressInformation['total'],
        ];
    }
}
