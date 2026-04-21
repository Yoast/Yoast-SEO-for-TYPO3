<?php

/**
 * This file is part of the "yoast_seo" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Service\Overview\Dto;

final readonly class DataProviderRequest
{
    public function __construct(
        protected int $id = 0,
        protected int $language = 0,
        protected string $table = '',
    ) {}

    public function getId(): int
    {
        return $this->id;
    }

    public function getLanguage(): int
    {
        return $this->language;
    }

    public function getTable(): string
    {
        return $this->table;
    }
}
