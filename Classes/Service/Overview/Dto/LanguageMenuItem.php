<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Service\Overview\Dto;

class LanguageMenuItem
{
    public function __construct(
        protected string $title = '',
        protected string $href = '',
        protected bool $active = false,
    ) {}

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getHref(): string
    {
        return $this->href;
    }

    public function setHref(string $href): void
    {
        $this->href = $href;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }
}
