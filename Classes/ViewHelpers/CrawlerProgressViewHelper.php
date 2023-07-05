<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\ViewHelpers;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use YoastSeoForTypo3\YoastSeo\Service\CrawlerService;

class CrawlerProgressViewHelper extends AbstractViewHelper
{
    public function initializeArguments(): void
    {
        $this->registerArgument('site', 'integer', 'Site root pageid', true);
        $this->registerArgument('language', 'integer', 'Language id', true);
    }

    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ): ?array {
        $crawlerService = GeneralUtility::makeInstance(CrawlerService::class);
        $progressInformation = $crawlerService->getProgressInformation($arguments['site'], $arguments['language']);

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
            'total' => $progressInformation['total']
        ];
    }
}
