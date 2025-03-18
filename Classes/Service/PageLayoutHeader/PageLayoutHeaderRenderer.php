<?php

/**
 * This file is part of the "yoast_seo" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Service\PageLayoutHeader;

use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use YoastSeoForTypo3\YoastSeo\Service\StandaloneView\StandaloneViewServiceInterface;

#[Autoconfigure(public: true)]
class PageLayoutHeaderRenderer
{
    public function __construct(
        protected StandaloneViewServiceInterface $standaloneViewService
    ) {}

    public function render(): string
    {
        return $this->standaloneViewService->render('PageLayout/Header');
    }
}
