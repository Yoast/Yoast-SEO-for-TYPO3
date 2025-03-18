<?php

/**
 * This file is part of the "yoast_seo" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Dto;

final readonly class RequestData
{
    public function __construct(
        protected int $pageId,
        protected int $languageId,
        protected string $additionalGetVars = ''
    ) {}

    /**
     * @return array<string, int|string>
     */
    public function toArray(): array
    {
        return [
            'pageId' => $this->pageId,
            'languageId' => $this->languageId,
            'additionalGetVars' => $this->additionalGetVars,
        ];
    }
}
