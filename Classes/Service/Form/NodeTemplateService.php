<?php

/**
 * This file is part of the "yoast_seo" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Service\Form;

use YoastSeoForTypo3\YoastSeo\Service\StandaloneView\StandaloneViewServiceInterface;

class NodeTemplateService
{
    public function __construct(
        protected StandaloneViewServiceInterface $standaloneViewService
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public function renderView(string $template, array $data = []): string
    {
        return $this->standaloneViewService->render(
            template: 'TCA/' . $template,
            data: $data,
            partialRootPath: 'EXT:yoast_seo/Resources/Private/Partials/TCA'
        );
    }
}
