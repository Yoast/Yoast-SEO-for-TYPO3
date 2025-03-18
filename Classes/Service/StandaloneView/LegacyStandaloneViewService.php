<?php

/**
 * This file is part of the "yoast_seo" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Service\StandaloneView;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * Legacy service to support rendering Fluid templates in TYPO3 v12.
 * Can be removed if v12 support is dropped.
 */
final readonly class LegacyStandaloneViewService implements StandaloneViewServiceInterface
{
    /**
     * @param array<string, mixed> $data
     */
    public function render(
        string $template,
        array $data = [],
        string $templateRootPath = 'EXT:yoast_seo/Resources/Private/Templates/',
        string $partialRootPath = 'EXT:yoast_seo/Resources/Private/Partials/'
    ): string {
        $templateView = GeneralUtility::makeInstance(StandaloneView::class);
        $templateView->setTemplateRootPaths([$templateRootPath]);
        $templateView->setPartialRootPaths([$partialRootPath]);
        $templateView->setTemplate($template);
        return $templateView->assignMultiple($data)->render();
    }
}
