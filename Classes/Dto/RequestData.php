<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Dto;

class RequestData
{
    public function __construct(
        protected readonly int $pageId,
        protected readonly int $languageId,
        protected readonly string $additionalGetVars = ''
    ) {}

    public function toArray(): array
    {
        return [
            'pageId' => $this->pageId,
            'languageId' => $this->languageId,
            'additionalGetVars' => $this->additionalGetVars,
        ];
    }
}
