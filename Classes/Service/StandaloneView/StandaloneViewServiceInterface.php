<?php

/**
 * This file is part of the "yoast_seo" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Service\StandaloneView;

interface StandaloneViewServiceInterface
{
    /**
     * @param array<string, mixed> $data
     */
    public function render(
        string $template,
        array $data = [],
        string $templateRootPath = 'EXT:yoast_seo/Resources/Private/Templates/',
        string $partialRootPath = 'EXT:yoast_seo/Resources/Private/Partials/'
    ): string;
}
