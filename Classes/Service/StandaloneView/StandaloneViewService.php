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
use TYPO3\CMS\Core\View\ViewFactoryData;
use TYPO3\CMS\Core\View\ViewFactoryInterface;

final readonly class StandaloneViewService implements StandaloneViewServiceInterface
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
        $viewFactory = GeneralUtility::makeInstance(ViewFactoryInterface::class);
        $viewFactoryData = new ViewFactoryData(
            templateRootPaths: [$templateRootPath],
            partialRootPaths: [$partialRootPath],
        );
        $view = $viewFactory->create($viewFactoryData);
        $view->assignMultiple($data);
        return $view->render($template);
    }
}
